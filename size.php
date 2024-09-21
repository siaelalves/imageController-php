<?php
namespace images ;

class size {
 
 public string $name;
 public dimension $dimension ;

 public function __construct ( $size_name ) {
  global $admin ;

  foreach ( $admin->imageSizes as $image_size ) {
   
   $this->dimension = new dimension ( ) ;
   
   if ( $size_name == $image_size [ "name" ] ) {

    $this->name = $image_size [ "name" ] ;

    if ( $image_size [ "width" ] == "auto" ) {

     $this->dimension->width = 0 ;

    } else {

     $this->dimension->width = $image_size [ "width" ] ;

    }

    if ( $image_size [ "height" ] == "auto" ) {

     $this->dimension->height = 0 ;

    } else {

     $this->dimension->height = $image_size [ "height" ] ;
     
    }

   } else if ( $size_name == "original" ) {

    $this->name = $size_name ;
    $this->dimension->width = 0 ;
    $this->dimension->height = 0 ;

   } else {

    $this->name = "original" ;
    $this->dimension->width = 0 ;
    $this->dimension->height = 0 ;

   }

  }
  
 }

}