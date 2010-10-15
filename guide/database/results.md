# Results

[DB::select] will return a Database_Result which you can iterate on or return as an array. This example shows how you can iterate through the Database_Result using a foreach.

	$results = DB::select()->from('users')->where('verified', '=', 0)->execute();
	foreach($results as $user)
	{
		//send reminder email to $user->email
		echo $user->email." needs to verfiy his/her account\n";
	}

Alternatively, you can access the Database_Result object like you would as an array.

	
	$cars = DB::select()->from('cars')->where('year', '<', '1970')->limit(1)->execute();
	if(count($cars))
	{
		echo 'Found '.$cars[0]['make'].' '.$cars[0]['model'];
	}
	

[DB::insert] returns an array of two values: the last insert id and the number of affected rows
	
	$insert = DB::insert('tools')
		->columns(array('name', 'model', 'description'))
		->values(array('Skil 3400 10" Table Saw', '3400', 'Powerful 15 amp motor; weighs just 54-pounds'));
		
	list($insert_id, $affected_rows) = $insert->execute();

[DB:delete] and [DB:update] both return the number of affected rows as an integer

	$rows_updated = DB::delete('tools')->where('model', 'like', '3400')->execute();
