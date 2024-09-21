<?php
namespace images ;

use Exception;
use GdImage;

class image {

 public \file_system\file $file ;
 public dimension $dimensions ;
 public float $ratio ;
 public \file_system\mime $mime ;
 public metadata|string $metadata ;
 private \GDImage $gdimage ;
 private \GDImage $resampled ;

 public function __construct ( \file_system\file $file ) {
  
  $this->file = $file ;  
  $this->dimensions = $this->get_dimensions ( ) ;
  $this->ratio = $this->get_ratio ( ) ;
  $this->mime = $file->mime ;
  $this->metadata = $this->get_metadata ( ) ;
  $this->gdimage = $this->get_gd_image ( ) ;
  
 }

 public function get_dimensions ( ) : dimension {

  $dimension = new dimension ( ) ;
  $dimension->width = getimagesize ( $this->file->name->full_name ) [ 0 ] ;
  $dimension->height = getimagesize ( $this->file->name->full_name ) [ 1 ] ;
  
  return $dimension ;

 }

 private function get_metadata ( ) : metadata | string {

  if ( $this->mime->type == "image") {

   if ( $this->mime->sub_type == "jpg" || $this->mime->sub_type == "jpeg" ) {

    return new metadata ( $this->file ) ;

   }

   return "" ;

  }

  return "" ;

 }

 private function get_ratio ( ) : float {

  return $this->dimensions->width / $this->dimensions->height ;

 }

 /**
  * Salva a imagem como um novo arquivo e qualidade. Se a imagem estiver no formato 
  * PNG, ainda assim a qualidade deve ser fornecida num intervalo entre 0 e 100. 
  * Nesse caso, a função irá dividir o valor por 10, arrendondará para cima se estiver 
  * acima de 0.5 e retornará o valor inteiro do resultado. Caso o resultado seja 10, 
  * então, a qualdiade a ser usada para a imagem PNG será a máxima: 9.
  */
 public function save ( string $file_name , int $quality = 100 ) {

  if ( $this->mime->type == "image" ) {

   if ( $this->mime->sub_type == "jpeg" || $this->mime->sub_type == "jpg" ) {

    return imagejpeg ( $this->resampled , $file_name , $quality ) ;

   }

   if ( $this->mime->sub_type == "png" ) {

    $quality = intval ( round ( $quality / 9 , 2 , PHP_ROUND_HALF_UP ) ) ;    
    if ( $quality >= 10 ) { $quality = 10 ; }

    return imagepng ( $this->resampled , $file_name , $quality ) ;

   }

  }

 }

 private function get_gd_image ( ) {

  if ( $this->mime->type == "image" ) {

   if ( $this->mime->sub_type == "jpeg" || $this->mime->sub_type == "jpg" ) {

    return imagecreatefromjpeg ( $this->file->name->full_name ) ;

   }

   if ( $this->mime->sub_type == "png" ) {

    return imagecreatefrompng ( $this->file->name->full_name ) ;

   }

  }

 }

 private function rotate ( int $angle , \GDImage $gdimage ) {

  return imagerotate ( $gdimage , $angle , 0 ) ;

 }

 private function flip ( Flip_Direction $direction ) {

  imageflip ( $this->gdimage , $direction->value ) ;
  imageflip ( $this->resampled , $direction->value ) ;

 }

 private function fix_rotation ( ) {

  $exif = exif_read_data ( $this->file->name->full_name ) ;

  if ( !empty ( $exif [ 'Orientation' ] ) && in_array ( $exif [ 'Orientation' ] , [2,3,4,5,6,7,8] ) ) {

   if ( in_array ( $exif [ 'Orientation' ] , [3,4] ) ) {

    $this->gdimage = $this->rotate ( 180 , $this->gdimage ) ;
    $this->resampled = $this->rotate ( 180 , $this->resampled ) ;

   }

   if ( in_array ( $exif [ 'Orientation' ] , [5,6] ) ) {
    
    $this->gdimage = $this->rotate ( -90 , $this->gdimage ) ;
    $this->resampled = $this->rotate ( -90 , $this->resampled ) ;
    
   }

   if ( in_array ( $exif [ 'Orientation' ] , [7,8] ) ) {

    $this->gdimage = $this->rotate ( 90 , $this->gdimage ) ;
    $this->resampled = $this->rotate ( 90 , $this->resampled ) ;

   }

   if ( in_array ( $exif [ 'Orientation' ] , [ 2,5,7,4] ) ) {
    
    $this->flip ( Flip_Direction::Horizontal ) ;

   }

  }

 }

 public function resize ( dimension $new_dimension , bool $keep_ratio = false ) : GdImage {

  if ( $keep_ratio == true ) {

   $new_dimension = $this->fix_dimension ( $new_dimension ) ;

  }

  $this->resampled = imagecreatetruecolor ( $new_dimension->width , $new_dimension->height ) ;

  // $destiny = new \coordinates\position ( ) ;
  // $source = new \coordinates\position ( ) ;
  
  // $this->get_coordinates ( $this->dimensions , $new_dimension ) ;

  imagecopyresampled ( $this->resampled , $this->gdimage , 0 , 0 , 0 , 0 , $new_dimension->width , $new_dimension->height , $this->dimensions->width , $this->dimensions->height ) ;
  
  $this->fix_rotation ( ) ;

  return $this->resampled ;

 }

 private function get_coordinates ( dimension $current , dimension $new ) : \coordinates\position {

  $position = new \coordinates\position ( 0 , 0 ) ;

  // obtém a posição x
  if ( $new->width == $current->width ) {
   
   $position->x ;

  } else if ( $new->width > $current->width ) {

   $position->x = ( $new->width - $current->width ) / 2 ;

  }

  // obtém a posição y
  if ( $new->height == $current->height ) {
   
   $position->y ;

  } else if ( $new->height > $current->height ) {

   $position->y = ( $new->height - $current->height ) / 2 ;

  }

  return $position ;

 }

 /**
  * Obtém um novo tamanho de imagem que seja proporcional ao original.
  */
 private function fix_dimension ( dimension $dimension ) : dimension {

  $new_dimension = new dimension ( ) ;

  if ( $dimension->width / $dimension->height > $this->ratio ) {

   $new_dimension->width = $dimension->height * $this->ratio ;
   $new_dimension->height = $dimension->height ;

  } else {

   $new_dimension->width = $dimension->width ;
   $new_dimension->height = $dimension->width / $this->ratio ;   

  }

  return $new_dimension ;

 }

}

?>