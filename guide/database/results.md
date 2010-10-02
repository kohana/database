# Results

SELECT returns a Database_Result which you can iterate or return as an array

	// examples go here

INSERT returns an array of insert id and affected rows
	
	$insert = DB::insert(...

	list($insert_id, $affected_rows) = $insert->execute();

DELETE and UPDATE both return the number of affected rows

	// moar examples