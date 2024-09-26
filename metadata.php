<?php
namespace images ;

/**
 * Ajuda a manipular os metadados padrão de uma imagem obtidos através de exif_read_data ( ).
 * Por padrão, ele retorna os seguintes metadados: "FileName, FileDateTime, FileSize, FileType, MimeType, SectionsFound, COMPUTED". 
 * Dependendo do fabricante da câmera pode retornar outros.
 */
class metadata {

 public string $file_name ;
 public string $file_date_time ;
 public int $file_size ;
 public string $file_type ;
 public string $mime_type ;
 public array $sections_found ;
 public array $computed ;

 public array $custom ;

 public function __construct ( \file_system\file $file ) {

  $metadata = exif_read_data ( $file->name->full_name ) ;

  $this->file_name = $metadata [ "FileName" ] ;
  $this->file_date_time = $metadata [ "FileDateTime" ] ;
  $this->file_size = $metadata [ "FileSize" ] ;
  $this->file_type = $metadata [ "FileType" ] ;
  $this->mime_type = $metadata [ "MimeType" ] ;
  $this->sections_found = explode ( "," , $metadata [ "SectionsFound" ] ) ;
  $this->computed = $metadata [ "COMPUTED" ] ;

  $this->custom = array_slice ( $metadata , 7 ) ;

 }
 
}