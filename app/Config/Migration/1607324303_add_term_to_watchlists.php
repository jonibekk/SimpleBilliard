<?php
class AddTermToWatchlists extends CakeMigration {
	  public $description = 'add_term_to_watchlists';

	  public $migration = array(
        'up' => array(
            'create_field' => array(
                'watchlists' => array(
                    'term_id' => array(
                        'type' => 'biginteger', 
                        'null' => true, 
                        'default' => null, 
                        'unsigned' => true, 
                        'comment' => 'term linked to the watchlists', 
                    ),
                ),
            ),
        ),
        'down' => array(
            'drop_field' => array(
                'watchlists' => array('term_id'),
            ),
        ),
    );

	  public function before($direction) {
		    return true;
	  }

	  public function after($direction) {
		    return true;
	  }
}
