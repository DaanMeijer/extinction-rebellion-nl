<?php

/**
 * simple php SDK for Action Network API v2
 * verson 1.2 - October 2016
 * author: Jonathan Kissam, jonathankissam.com
 * API documentation: https://actionnetwork.org/docs
 */

class ActionNetwork {

	private $api_key = 'PASS_API_KEY_WHEN_INSTANTIATING_CLASS';
	private $api_version = '2';
	private $api_base_url = 'https://actionnetwork.org/api/v2/';

	public function __construct($api_key = null) {
		if(!extension_loaded('curl')) trigger_error('ActionNetwork requires PHP cURL', E_USER_ERROR);
		if(is_null($api_key)) trigger_error('api key must be supplied', E_USER_ERROR);
		$this->api_key = $api_key;
	}

	public function call($endpoint, $method = 'GET', $object = null) {
		
		// if endpoint is passed as an absolute URL (i.e., if it came from an API response), remove the base URL
		$endpoint = str_replace($this->api_base_url,'',$endpoint);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		if ($method == "POST") {
			curl_setopt($ch, CURLOPT_POST, 1);
			if ($object) {
				$json = json_encode($object);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					'OSDI-API-Token: '.$this->api_key,
					'Content-Type: application/json',
					'Content-Length: ' . strlen($json))
				);
			}
		} else {
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('OSDI-API-Token:'.$this->api_key));
		}
		curl_setopt($ch, CURLOPT_URL, $this->api_base_url.$endpoint);

		$response = curl_exec($ch);

		curl_close($ch);

		return json_decode($response);
	}

	// helper functions for collections

	public function getResourceId($resource) {
		if (!isset($resource->identifiers) || !is_array($resource->identifiers)) { return null; }
		foreach ($resource->identifiers as $identifier) {
			if (substr($identifier,0,15) == 'action_network:') { return substr($identifier,15); }
		}
	}

	public function getResourceTitle($resource) {
		if (isset($resource->title)) { return $resource->title; }
		if (isset($resource->name)) { return $resource->name; }
		if (isset($resource->email_addresses) && is_array($resource->email_addresses) && count ($resource->email_addresses)) {
			if (isset($resource->email_addresses[0]->address)) {
				return $resource->email_addresses[0]->address;
			}
		}
	}
	
	public function getNextPage($response) {
		return isset($response->_links) && isset($response->_links->next) && isset($response->_links->next->href) ? $response->_links->next->href : false;
	}
	
	// get embed codes
	public function getEmbedCodes($resource, $array = false) {
		$embed_endpoint = isset($resource->_links->{'action_network:embed'}->href) ? $resource->_links->{'action_network:embed'}->href : '';
		if (!$embed_endpoint) { return $array ? array() : null; }
		$embed_codes = $this->call($embed_endpoint);
		return $array ? (array) $embed_codes : $embed_codes;
	}

	public function simplifyCollection($response, $endpoint) {
		$osdi = 'osdi:'.$endpoint;
		$collection = array();
		if (isset($response->_embedded->$osdi)) {
			$collection_full = $response->_embedded->$osdi;
			foreach ($collection_full as $resource) {
				$resource_id = $this->getResourceId($resource);
				$resource_title = $this->getResourceTitle($resource);
				$collection[] = array('id' => $resource_id, 'title' => $resource_title); 
			}
		}
		return $collection;
	}

	// fetch collections

	public function getCollection($endpoint, $page = 1, $per_page = null) {
		if ($page > 1) { $endpoint .= '?page='.$page; }
		if ($per_page) { $endpoint .= ( ($page > 1) ? '&' : '?') . 'per_page=' . $per_page; }
		return $this->call($endpoint);
	}

	public function getSimpleCollection($endpoint, $page = 1, $per_page = null) {
		$response = $this->getCollection($endpoint, $page, $per_page);
		return $this->simplifyCollection($response, $endpoint);
	}

	public function getFullSimpleCollection($endpoint) {
		$response = $this->getCollection($endpoint);
		if (isset($response->total_pages)) {
			if ($response->total_pages > 1) {
				$full_simple_collection = $this->simplifyCollection($response, $endpoint);
				for ($page=2;$page<=$response->total_pages;$page++) {
					$response = $this->getCollection($endpoint, $page);
					$full_simple_collection = array_merge($full_simple_collection, $this->simplifyCollection($response, $endpoint));
				}
				return $full_simple_collection;
			} else {
				return $this->simplifyCollection($response, $endpoint);
			}
		} else {
			$full_simple_collection = $this->simplifyCollection($response, $endpoint);
			$next_page = $this->getNextPage($response);
			while ($next_page) {
				$response = $this->getCollection($next_page);
				$full_simple_collection = array_merge($full_simple_collection, $this->simplifyCollection($response, $endpoint));
				$next_page = $this->getNextPage($response);
			}
			return $full_simple_collection;
		}
	}

	/**
	 * Traverse Collections
	 *
	 * if you are using a class that extends ActionNetwork,
	 * this method will first test to see if $callback is a defined method
	 * of your class. If not it will be treated as the name of a php function.
	 *
	 * It will be passed the following variables:
	 * $resource : the ActionNetwork resource object
	 * $endpoint : the endpoint passed to traverseCollection or traverseFullCollection
	 * $index : the order of the resource in the list
	 * $total : the total number of resources in the page or collection
	 * $this : if an independent php function, will be passed the ActionNetwork object
	 */
	public function traverseCollection($endpoint, $callback) {
		$response = $this->getCollection($endpoint);
		$this->traverseCollectionPage($endpoint, $response, $callback);
		return $response;
	}

	public function traverseFullCollection($endpoint, $callback) {
		$response = $this->getCollection($endpoint);
		$this->traverseCollectionPage($endpoint, $response, $callback);
		if ( isset($response->total_pages) && ($response->total_pages > 1) ) {
			for ($page=2;$page<=$response->total_pages;$page++) {
				$response = $this->getCollection($endpoint, $page);
				$this->traverseCollectionPage($endpoint, $response, $callback);
			}
		} else {
			$next_page = $this->getNextPage($response);
			while ($next_page) {
				$response = $this->getCollection($next_page);
				$this->traverseCollectionPage($endpoint, $response, $callback);
				$next_page = $this->getNextPage($response);
			}
		}
	}

	private function traverseCollectionPage($endpoint, $response, $callback) {
		if (!is_string($callback)) { return; }
		if (method_exists($this, $callback)) {
			$callback_method = 'object_method';
		} else {
			$callback_method = 'function_name';
			if (!function_exists($callback)) { return; }
		}
		$osdi = 'osdi:'.$endpoint;
		$total = $response->total_records;
		$index = ($response->page - 1) * $response->per_page + 1;
		if (isset($response->_embedded->$osdi)) {
			$collection = $response->_embedded->$osdi;
			foreach ($collection as $resource) {
				if ($callback_method == 'object_method') {
					$this->$callback($resource, $endpoint, $index, $total);
				} else {
					$callback($resource, $endpoint, $index, $total, $this);
				}
			}
		}
	}

	// get simple lists (id and title) of petitions, events, fundraising pages, advocacy campaigns, forms and tags

	public function getAllPetitions() {
		return $this->getFullSimpleCollection('petitions');
	}

	public function getAllEvents() {
		return $this->getFullSimpleCollection('events');
	}

	public function getAllFundraisingPages() {
		return $this->getFullSimpleCollection('fundraising_pages');
	}

	public function getAllAdvocacyCampaigns() {
		return $this->getFullSimpleCollection('advocacy_campaigns');
	}

	public function getAllForms() {
		return $this->getFullSimpleCollection('forms');
	}

	public function getAllTags() {
		return $this->getFullSimpleCollection('tags');
	}

	// get embeds for a petition, event, fundraising page, advocacy campaign or form

	public function getEmbed($type, $id, $size = 'standard', $style = 'default') {
		if (!in_array($type, array('petitions', 'events', 'fundraising_pages', 'advocacy_campaigns', 'forms')))
			trigger_error('getEmbed must be passed a type of petitions, events, fundraising_pages, advocacy_campaigns or forms', E_USER_ERROR);
		if (!in_array($size, array('standard', 'full'))) trigger_error('getEmbed must be passed a size of standard or full', E_USER_ERROR);
		if (!in_array($style, array('default', 'layout_only', 'no'))) trigger_error('getEmbed must be passed a style of default, layout_only or no', E_USER_ERROR);
		$embeds = $this->call($type.'/'.$id.'/embed');
		$selector = 'embed_'.$size.'_'.$style.'_styles';
		return $embeds->$selector;
	}

	// pass $person as an ActionNetworkPerson object, an associative array or an object to use helpers

	public function signupPerson($person, $tags = null) {
		$person_object = is_a($person, 'ActionNetworkPerson') ? $person : new ActionNetworkPerson($person);
		$object = (object) array('person' => $person_object);
		if (is_array($tags)) {
			$object->add_tags = $tags;
		}
		return $this->call('people','POST',$object);
	}

	public function recordAttendance($person, $event_id, $tags = null) {
		$person_object = is_a($person, 'ActionNetworkPerson') ? $person : new ActionNetworkPerson($person);
		$object = (object) array('person' => $person_object);
		if (is_array($tags)) {
			$object->add_tags = $tags;
		}
		return $this->call('events/'.$event_id.'/attendances','POST',$object);
	}

	public function recordSignature($person, $petition_id, $comment = null, $tags = null) {
		$person_object = is_a($person, 'ActionNetworkPerson') ? $person : new ActionNetworkPerson($person);
		$object = (object) array('person' => $person_object);
		if ($comment) { $object->comments = $comment; }
		if (is_array($tags)) {
			$object->add_tags = $tags;
		}
		return $this->call('petitions/'.$petition_id.'/signatures','POST',$object);
	}

	public function recordSubmission($person, $form_id, $tags = null) {
		$person_object = is_a($person, 'ActionNetworkPerson') ? $person : new ActionNetworkPerson($person);
		$object = (object) array('person' => $person_object);
		if (is_array($tags)) {
			$object->add_tags = $tags;
		}
		return $this->call('forms/'.$form_id.'/submissions','POST',$object);
	}

}

/**
 * instantiate ActionNetworkPerson with an associative array or object
 * following this schema: https://actionnetwork.org/docs/v2/people/
 * OR a simplified associative array or object:
 * $person->email or $person['email'] can be used to set the primary email address
 * $person->status or $person['status'] can be used to set the subscription status
 * $person->address_lines, ->locality, ->region, ->postal_code and ->country can be used to set primary address properties
 * $person->[any other property], if it is a string, will be used to set a custom field
 * OR $person->address or $person['address'] can be set to a single postal address array or object
 */
class ActionNetworkPerson {

	public $family_name = null;
	public $given_name = null;
	public $postal_addresses = array();
	public $email_addresses = array();
	public $custom_fields;

	private $valid_subscription_statuses = array('subscribed', 'unsubscribed', 'bouncing', 'spam complaint');
	private $valid_address_fields = array('primary','address_lines','locality','region','postal_code','country');

	public function __construct($person = null) {
		if (is_array($person)) { $person = (object) $person; }
		if (!is_object($person)) trigger_error('person must be passed as an associative array or object', E_USER_ERROR);
		if (isset($person->email) && filter_var($person->email, FILTER_VALIDATE_EMAIL)) {
			$person->email_addresses[0] = (object) array('address' => $person->email);
		}
		if (isset($person->email_addresses) && is_array($person->email_addresses)) {
			foreach($person->email_addresses as $index => $email_address) {
				if (is_array($email_address)) { $person->email_addresses[$index] = (object) $email_address; }
			}
		}
		if (!isset($person->email_addresses[0]->address) || !filter_var($person->email_addresses[0]->address, FILTER_VALIDATE_EMAIL)) trigger_error('person must include a valid email address', E_USER_ERROR);
		$this->email_addresses = $person->email_addresses;

		if (isset($person->status) && in_array($person->status, $this->valid_subscription_statuses)) {
			$this->email_addresses[0]->status = $person->status;
		}

		if (isset($person->family_name)) { $this->family_name = $person->family_name; }
		if (isset($person->given_name)) { $this->given_name = $person->given_name; }

		foreach ($this->valid_address_fields as $field) {
			if ($field == 'primary') { continue; }
			if (isset($person->$field)) {
				if (!isset($person->address) || !is_object($person->address)) { $person->address = new stdClass(); }
				if ($field == 'address_lines') {
					$person->address->address_lines = array($person->address_lines);
				} else {
					$person->address->$field = $person->$field;
				}
			}
		}

		if (isset($person->address)) {
			$address = $person->address;
			if (is_array($address)) { $address = (object) $address; }
			if (!is_object($address)) trigger_error('address must be passed as an associative array or object', E_USER_ERROR);
			if (!isset($address->primary)) { $address->primary = true; }
			$valid_address = new stdClass();
			foreach($this->valid_address_fields as $field) {
				if (isset($address->$field) && ( ($field=='address_lines') ? is_array($address->$field) : true ) ) {
					$valid_address->$field = $address->$field;
				}
			}
			$person->postal_addresses[] = $valid_address;
		}

		if (isset($person->postal_addresses) && is_array($person->postal_addresses)) {
			foreach($person->postal_addresses as $index => $postal_address) {
				if (is_array($postal_address)) { $person->postal_addresses[$index] = (object) $postal_address; }
			}
			$this->postal_addresses = $person->postal_addresses;
		}

		$person_as_array = (array) $person;
		foreach ($person_as_array as $key => $value) {
			if (is_string($value) && !in_array($key, $this->valid_address_fields) && !in_array($key,array('email','status','family_name','given_name'))) {
				if (!isset($person->custom_fields) || !is_object($person->custom_fields)) { $person->custom_fields = new stdClass(); }
				$person->custom_fields->$key = $value;
			}
		}

		$this->custom_fields = new stdClass();
		if (isset($person->custom_fields)) {
			$custom_fields = $person->custom_fields;
			if (is_array($custom_fields)) {
				$custom_fields = (object) $custom_fields;
			}
			if (is_object($custom_fields)) {
				$this->custom_fields = $custom_fields;
			}
		}

	}

	public function setSubscriptionStatus($status) {
		if (in_array($status, $this->valid_subscription_statuses)) {
			$this->email_addresses[0]->status = $status;
		}
	}

	public function addFamilyName($family_name = null) {
		$this->family_name = $family_name;
	}

	public function addGivenName($given_name = null) {
		$this->given_name = $given_name;
	}

	// pass address as an associative array or object
	public function addPostalAddress($address = null) {
		if (is_array($address)) { $address = (object) $address; }
		if (!is_object($address)) trigger_error('address must be passed as an associative array or object', E_USER_ERROR);
		$valid_address = new stdClass();
		foreach($this->valid_address_fields as $field) {
			if (isset($address->$field) && ( ($field=='address_lines') ? is_array($address->$field) : true ) ) {
				$valid_address->$field = $address->$field;
			}
		}
		$this->postal_addresses[] = $valid_address;
	}

	public function addCustomField($key_or_array, $value = null) {
		if (is_array($key_or_array)) {
			foreach($key_or_array as $k => $v) {
				$this->custom_fields->$k = $v;
			}
		} elseif (is_string($key_or_array) && $value) {
			$this->custom_fields->$key_or_array = $value;
		}
	}

}
