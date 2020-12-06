
<form action="<?php echo esc_url( home_url( '/' ) ); ?>" class="form-inline search-form" role="search" method="get">
  <div class="form-group mb-2 mr-2">
    <label for="inputPassword2" class="sr-only">Password</label>
    <input type="search" class="form-control" placeholder="<?php echo esc_attr_x( 'Search &hellip;', 'placeholder', 'theme-xrnl' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
  </div>
  <button type="submit" class="btn btn-black mb-2">
    <?php echo _x( 'Search', 'submit button', 'theme-xrnl' ); ?>
  </button>
</form>
