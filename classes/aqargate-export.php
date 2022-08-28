<?php
class AqarGate_Export {

    public function __construct() {
        $this->init_actions();
    }

    public function init_actions(){}

    public static function array_csv_download( array &$array, $filename = "export.csv", $delimiter=";" )
    {
        if (count($array) == 0) {
            return null;
          }
          header( 'Content-Type: application/csv' );
          header( 'Content-Disposition: attachment; filename="' . $filename . '";' );
        
        // clean output buffer
        
        $handle = fopen( 'php://output', 'w' );
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
        // use keys as column titles
        fputcsv( $handle, array_keys(reset($array) ), $delimiter );
  
        foreach ( $array as $value ) {
            fputcsv( $handle, $value, $delimiter );
        }
        
        fclose( $handle );
    
        // flush buffer
        
        
        // use exit to get rid of unexpected output afterward
        exit();
    }

}