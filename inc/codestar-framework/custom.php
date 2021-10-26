<?php 

// Control core classes for avoid errors
if( class_exists( 'CSF' ) ) {

  //
  // Set a unique slug-like ID
  $prefix = 'wms_options';

  //
  // Create a metabox
  CSF::createMetabox( $prefix, array(
    'title'     => 'Necessary Options',
    'post_type' => 'saas-sites',
  ) );

  //
  // Create a section
  CSF::createSection( $prefix, array(
    'fields' => array(
      array(
        'id'    => 'website-url',
        'type'  => 'text',
        'title' => 'Website URL',
      ),
      array(
        'id'    => 'root-token',
        'type'  => 'text',
        'title' => 'Root Token',
      ),
      array(
        'id'    => 'user-token',
        'type'  => 'text',
        'title' => 'User Token',
      ),
      array(
        'id'    => 'user-id',
        'type'  => 'number',
        'title' => 'User ID',
      ),
      array(
        'id'    => 'status',
        'type'  => 'checkbox',
        'title' => 'Status',
        'label' => 'taken'
      ),

    )
  ) );


}

