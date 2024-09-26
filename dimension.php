<?php
namespace images ;

class dimension {

 /** Largura da imagem em px. */
 public int $width ;
 /** Altura da imagem em px. */
 public int $height ;

 public function __construct ( int $width = 0 , int $height = 0 ) {

  $this->width = $width ;
  $this->height = $height ;

 }

 /**
  * Obtém uma string com altura e largura como: "1280 x 720 px".
  */
  public function to_string ( ) {
   return $this->height .  " por " . $this->width . " px" ;
  }
  
}

?>