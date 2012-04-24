<?php
/*
* A list of little functions that I often use to help me out
* @author Ashley Banks
*/





/**  		Array Functions 		**/



/**
 * Displays an array in a tidy format within the browser.
 * Can either return the array as a string or kill the page displaying it
 *
 * @param array $array - the array to show
 * @param bool $debug - switch to FALSE to return a string
 * @return mixed - die or string
 */
function arrshow ( $array, $debug = TRUE )
{
	$count = 0;
	foreach ( $array as $key => $value )
	{
		if ( !!$key )
			$_key = $key;
		else
			$_key = $count;
			
		$output .= '[' . $_key . '] => '.$value . '</br>';
	}
	
	if ( $debug )
		die ( $output );
	else
		return $output;
}





/**
 * Reverses an array - making sure the array does not have any empty values
 * Used this on a project where I kept getting a random empty value so threw it in
 * 
 * @param array $array - the array
 * @return array @array - the array reversed
 */
function reverse_array ( $array )
{
	for ( $i=0; $i < count( $array ); $i++ )
		if ( $array[ $i ] == '' )
			unset ( $array[ $i ] );
		
	$array = array_reverse ( $array );	
	return $array;
}





/**
 * Builds a multi one dimensional array 
 * 		exmample:  array ( array ( 'stylesheet' => 'style1' ), array ( 'stylesheet' => 'style2' ) );
 *
 * @param mixed $items - the array of css of values 
 * @param string $key_title - the title of the key in the arrays
 * @param optional iterator - if a string this is what is exploded
 * @retun array
 */
function mutli_one_dimension ( $items, $key_title, $iterator = ',' )
{
	if ( !is_array ( $items ) )
		$items = explode ( $iterator, str_replace ( ' ', '', trim ( $items ) ) );
	
	$_array = array ();
	
	foreach ( $items as $value )
	{
		$_array[][ $key_title ] = $value;
	}
	
	return $_array;	
}







/** 		Miscellanious 		**/


/**
 * Gets the extension of a file.
 * 
 * @param string $file - the file to get the extension from
 * @return string - the extension
 */
function get_ext ( $file )
{
	$path_parts = pathinfo ( $file );
	return $path_parts['extension'];
}





/**
 * Check whether a field name is set.
 *
 * @param string $fieldname - the name of the field name
 * @return bool $outcome - the outcome of the check 
 */
function check ( $fieldname )
{
	$outcome = ( isset ( $_POST[ $fieldname ] ) ) ? TRUE : FALSE;	
	return $outcome;
}





/**
 * Quickly go to a location - no fancy headers sent though
 *
 * @param string $location - the location to go.
 */
function go ( $location )
{
	header ( 'location: '.$location );
	exit();	
}





/**
 * I found myself always pressing die ( "hi" ) to debug, or using print_r within to debug arrays
 * Just use this to kill the page with a result.
 * 
 * @param mixed $display - either an array or string you want to display.
 */
function debug ( $display = '' )
{
	if ( !!$display && is_array ( $display ) )
		$display = arrshow ( $display, FALSE );
	if ( !isset ( $display ) )
		$display = 'Hi';
	
	die ( $display );
			
}





/**
 * Downloads a given file.
 * 
 * @param string $filename - the name of the file to download
 * @param optional string $location - the location that the file is set. ( default is what I use ) 
 */
function download ( $filename, $location = 'Assets/Uploads/Documents/'  )
{
	if ( !$filename ) 
		die ( 'must provide a file to download!' );		
	else 
	{
	
		$path =  $location . '/' . $filename;
		
		if ( file_exists( $path ) && is_readable( $path ) ) {
			
			$size = filesize( $path );
			header( 'Content-Type: application/octet-stream' );
			//header( 'Content-Length: ' . $size );
			header( 'Content-Disposition: attachment; filename=' . $filename );
			header( 'Content-Transfer-Encoding: binary' );
		
			$file = fopen( $path, 'rb' );
		
			if ( $file ) 
			{
				fpassthru( $file );
				exit;
			} 
			else 
			{
				echo $err;
			}
		} 
		else 
			die ( 'Appears to be a problem with downloading that file.' );		
	}		
}






/**
 * Builds an array of files within a directory
 *
 * @param string $directory - the directory you want to build the array of
 * @param optional array $ignore - array of extensions to ignore
 * @return array $files - the array of files
 */
function build_array_files ( $directory, $ignore = array () )
{
	$files = array ();
	
	if ( is_dir ( $directory ) )
	{
		if ( $dh = opendir ( $directory ) )
		{
			while ( ( $file = readdir ( $dh ) ) !== FALSE )
			{
				if ( $file != '.' && $file != '..' )
				{	
					if ( is_array ( $ignore ) )
					{
						if ( !in_array ( get_ext ( $file ), $ignore ) )
							$files[] = $file;
					}
					else
						$files[] = $file;
				}
			}
			closedir ( $dh );
		}
		return $files;
	}
	else
		return FALSE;
}



/** 		URL Specific 		**/




/**
 * Grab a specific segment from the URI
 * 
 * @param int $num - the number of the segment to grab 
 * @return string - the item in the URI
 */
function uri_seg ( $num )
{
	$url = $_SERVER['REQUEST_URI'];
	
	$url = str_replace( URI_SPLIT , "" , $url) ;
	
	$uri = preg_split( '[\\/]', $url, -1, PREG_SPLIT_NO_EMPTY );	
	
	return $uri[ $num ];
}





/*
* Converts string into a friendly URL string
* 
* @param $string
* @return the friendly URL
*/
function friendly_url ( $string )
{
	$string = preg_replace( "`\[.*\]`U", "", $string );
	$string = preg_replace( '`&(amp;)?#?[a-z0-9]+;`i', '-', $string );
	$string = htmlentities( $string, ENT_COMPAT, 'utf-8' );
	$string = preg_replace( "`&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);`i", "\\1", $string );
	$string = preg_replace( array( "`[^a-z0-9]`i","`[-]+`") , "-", $string );
	return strtolower( trim( $string, '-' ) );
}






/** 	Strings specific functions 		**/





/**
 * Taken from CodeIgnitors helper functions - I use this alot. 
 * Limits the words within a paragraph
 */
function word_limiter ( $str, $limit = 100, $end_char = '&#8230;' )
{
	if ( trim( $str ) == '' )
		return $str;

	preg_match( '/^\s*+(?:\S++\s*+){1,' . (int)$limit .'}/', $str, $matches );

	if ( strlen( $str ) == strlen( $matches[0] ) )
		$end_char = '';

	return rtrim ( $matches[0] ) . $end_char;
}





/**
 * Creates a randomly generated string ( password )
 *
 * @param optional int $length - the length of the string.
 * @return string - the password
 */
function create_password ( $length = 7 )
{ 
	$chars = "abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ023456789"; 
	
	srand ( (double)microtime() * 1000000);
	
	$i = 0; 
	$pass = '' ; 
	while ( $i <= $length ) 
	{ 
		$num = rand() % 33; 
		$tmp = substr ( $chars, $num, 1 ); 
		$pass = $pass . $tmp; 
		$i++; 
	} 
	return $pass; 
}





/**
 * Inserts a given tag between a given string. Useful if a design
 * needs to have a tag opened and closed between one paragraph.
 *
 * @param string $string - the string to put shit in
 * @param int $num - the number of words to insert it in
 * @param optional string $tags - the tags to put in - default <span>
 * @return string $string - the string.
 */
function insert_tags ( $string, $num, $tags = '</span><span>' )
{
	// Explode the string from spaces.
	$string = explode ( " ", $string );
	
	$result = array();
	
    for( $i = 0; $i < sizeof( $string ); $i++ ) 
	{
		// Check that this matches our number and whether to insert tag
        if ( $i % $num == 0 && $i != 0 ) 
            $result[] = $tags;
      
        $result[] = $string[ $i ];
    }
	
	// Implode the result with some spaces.
    $result = implode( ' ', $result );
	
	return $result;
}





/**
 * Handy little function that replaces given tags with given arguments
 * Example: {0} would be replaced with the first argument given - format_string ( '{0}, 'banana' )
 * Credited to Mike Dean.
 * 
 * @param string $string - the string
 * @return formatted string.
 */
function format_string ( $the_string )
{
	for ( $i = 0; $i < func_num_args()-1; $i++ )
	{
		$arg = func_get_arg( $i+1 );
		$the_string = str_replace( "{" . $i . "}", $arg, $the_string );
	}
	
	return $the_string;
}
?>