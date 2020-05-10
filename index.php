<?php

// Available pack size
$pack_sizes = array( 250, 500, 1000, 2000, 5000 );

$no_widgets_original = '';
$order_packs_count = array();
if( isset( $_POST['action_form'] ) && $_POST['action_form'] == 'submit' )
{
	// Wrong values
	if( !isset( $_POST['no_widgets'] ) || trim( $_POST['no_widgets'] ) == '' || !is_numeric( $_POST['no_widgets'] ) )
	{
		header( 'Location: index.php' );
	}
	
	// Input values
	$no_widgets_original = $_POST['no_widgets'];
	$no_widgets = $_POST['no_widgets'];
	
	// Break the order values
	$order_packs = array();
	rsort( $pack_sizes );
	foreach( $pack_sizes as $pack_size )
	{
		while( $pack_size <= $no_widgets )
		{
			$order_packs[] = $pack_size;
			$no_widgets -= $pack_size;
		}
	}
	
	// Get the remaining
	sort( $pack_sizes );
	if( $no_widgets > 0 )
	{
		foreach( $pack_sizes as $pack_size )
		{
			while( $pack_size >= $no_widgets && $no_widgets > 0 )
			{
				$order_packs[] = $pack_size;
				$no_widgets = 0;
			}
		}
	}
	
	// Group by and count
	$order_packs_count = array_count_values( $order_packs );
	
	// Search for few packs as possible to fulfil each order
	foreach( $pack_sizes as $pack_size )
	{
		if( isset( $order_packs_count[$pack_size] ) && $order_packs_count[$pack_size] != 1 )
		{
			$sum_order_pack = $pack_size * $order_packs_count[$pack_size];
			if( in_array( $sum_order_pack, $pack_sizes ) )
			{
				// Increase the next
				if( !isset( $order_packs_count[$sum_order_pack] ) )
				{
					$order_packs_count[$sum_order_pack] = 1;
				}
				else
				{
					$order_packs_count[$sum_order_pack]++;
				}
				
				// Decrease
				$order_packs_count[$pack_size] = $order_packs_count[$pack_size] - 2;
			}
		}
		
	}
	
	// Clear zero
	foreach( $order_packs_count as $order_packs => $order_count )
	{
		if( $order_count == 0 )
		{
			unset( $order_packs_count[$order_packs] );
		}
	}
}
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body {
  font-family: Arial, Helvetica, sans-serif;
  background-color: black;
}

* {
  box-sizing: border-box;
}

/* Add padding to containers */
.container {
  padding: 16px;
  background-color: white;
}

/* Full-width input fields */
input[type=text], input[type=password] {
  width: 100%;
  padding: 15px;
  margin: 5px 0 22px 0;
  display: inline-block;
  border: none;
  background: #f1f1f1;
}

input[type=text]:focus, input[type=password]:focus {
  background-color: #ddd;
  outline: none;
}

/* Overwrite default styles of hr */
hr {
  border: 1px solid #f1f1f1;
  margin-bottom: 25px;
}

/* Set a style for the submit button */
.orderbtn {
  background-color: #4CAF50;
  color: white;
  padding: 16px 20px;
  margin: 8px 0;
  border: none;
  cursor: pointer;
  width: 100%;
  opacity: 0.9;
}

.orderbtn:hover {
  opacity: 1;
}

/* Add a blue text color to links */
a {
  color: dodgerblue;
}

/* Set a grey background color and center the text of the "sign in" section */
.signin {
  background-color: #f1f1f1;
  text-align: center;
}
</style>
</head>
<body>

<form action="index.php" method="POST">
  <div class="container">
    <h1>Wally’s Widget Company</h1>
    <p>Please fill in this form to order.</p>
    <hr>

    <label for="no_widgets"><b>Number of widgets</b></label>
    <input type="text" placeholder="Enter Number" name="no_widgets" id="no_widgets" required value="<?php echo $no_widgets_original; ?>">
	
	<?php
	if( count( $order_packs_count ) > 0 )
	{
		echo 'ORDER: <br />';
		foreach( $order_packs_count as $order_packs => $order_count )
		{
			echo $order_count . ' x PACK SIZE: ' . $order_packs . '<br/>';
		}
	}
	?>
	
    <button type="submit" class="orderbtn">Order</button>
	<input type="hidden" name="action_form" id="action_form" value="submit" />
  </div>
  
</form>

</body>
</html>
