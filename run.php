<?php

// Example class to hold windspeed detail information.
class WindSpeedDetails{
	// Store the filename and the line number. The values of the filename and the 
	// line number are stored in a generic property in the object
	private $fileName = null;
	private $lineNumber = null;

	// Details about the lines.
	private $date = null;
	private $windspeed = null;
	private $gust = null;

	// Constructor for the object - It needs to hold some unique, useful info.
	function __construct($fileName , $lineNumber ){
		#printf("\Construct for Object %s %s", $fileName , $lineNumber);
		// Store the filename and the line number in properties.
		$this->fileName = $fileName;
		$this->lineNumber = $lineNumber;
		}
	/*************************************
		Setters - Set Functions for the data.
	*************************************/
	public function setDate($date){
		$this->date = $date;
		#printf("\nDate: %s", $this->date );
	}
	
	public function setWindspeed($windspeed){
		$this->windspeed = $windspeed;
	}

	public function setGust($gust){
		$this->gust = $gust;	
	}
	
	/****************************
		Getters - Get Functions for the data.
	****************************/
	public function getDate(){
		#printf("\nDate: %s", $this->date );
		return 	$this->date;
	}
	public function getWindspeed(){
		#printf("\nWindspeed: %s", $this->windspeed );
		return $this->windspeed;	
	}
	public function getGust(){
		#printf("\nGust: %s", $this->gust );
		return $this->gust;	
	}
	# Fetch the reference information - Location of the file and line number.
	public function getReference(){
		# Format a reference string nicely.
		$reference = 'FileName: '. $this->fileName . 'Line: '  .$this->lineNumber;
		printf("\nReference: %s", $reference);
	}

}


function readInputData($fullPath){

	# Have an arrray to store the line input data objects.
	$outputArray = array();

	$fileHandle = fopen($fullPath,'r');
	$lineCounter = 0;
	fgetcsv($fileHandle,1024,',') ;// Ignore the first line
	while ( ($data = fgetcsv($fileHandle,1024,',') ) ){
		#printf("Data: %s", $data)	;
		#printf( "Count: %s\n", count($data) );
		if (count($data)>=10){
			$Date	= $data[0];
			$Time	= $data[1];
			$WSPD	= $data[2];
			$WD	= $data[3];
			$GST	= $data[4];
			$ATMP		= $data[5];
			$WTMP		= $data[6];
			$BARO		= $data[7];
			$DEPTH	= $data[8];
			$VIS 	= $data[9];

			#printf("\nDate: %s", $Date );

			$windspeed = new WindSpeedDetails("./dataCSV/response1_1.csv",$lineCounter);
			$windspeed->setDate($Date);
			$windspeed->setWindspeed($WSPD);
			$windspeed->setGust($GST);

			$windspeed->getDate();
			$windspeed->getWindspeed();
			$windspeed->getGust();
			#$windspeed->getReference();

			# Add the data item onto the end of the array.
			array_push($outputArray , $windspeed);

			

		}
		else{
			print("Input data length incorrect.");
		}	

	}

	fclose($fileHandle);
	# Return the outputArray data.
	return $outputArray;

}

# Loop around the directory and recover all the files. Parse them and then
# put them into a big object/array which holds everything.
function searchDirectory(){

	# Create an array to hold the line Details
	$collectionData = array();

	$arrayItems = scandir('./dataCSV/');
	#echo "\nDirectory Length: ".count($arrayItems);
	for ($count = 0 ; $count < count($arrayItems) ; $count++){
		#printf("\nItem: %s", $arrayItems[$count] );
		# Run some regex - confirm that the file pattern looks to be expected/valid.
		preg_match('/[a-z]{8,}[0-9]{1,}_[0-9]{1,}.csv$/', 
														$arrayItems[$count], 
														$matches
														);
		# Check to see how many matches have been found.
		if ( count($matches) > 0 ){
			# Confirm the CSV looks like valid regex.
			#printf("\nMatch Count: %s", count($matches));
			$fullPath = './dataCSV/'.$arrayItems[$count];
			#printf("\nFullPath: %s",$fullPath);

			# Read the input data
			$resultSet = readInputData($fullPath);
			# Merge and append the results together.
			$collectionData = array_merge($collectionData , $resultSet );	
		}
	}
	#printf("\nTotal Data Length: %s", count($collectionData) );
	return $collectionData;
}

# Loop around the averageStats. Look at the wind speed and the gust numbers. #
#	group these together to product an average.
function averageStats($inputData){

	$totalWindspeed = 0;
	$totalGust = 0;
	$totalCount = 0;

	for ($count = 0 ; $count < count($inputData) ; $count++){
		$referenceObject = $inputData[$count];
		if ($referenceObject instanceof WindSpeedDetails){
			# Add the windspeed to the total.			
			$totalWindspeed += $referenceObject->getWindspeed();
			$totalGust += $referenceObject->getGust();
			# Increment the total count
			$totalCount++;
		}
	}

	# Look at the total windspeed and attempt to produce an average.
	if ($totalCount > 0){
		# Check to see if the total windspeed is provided, if true average
		# this and output an average value.
		if ($totalWindspeed > 0){
			$averageSpeed = ($totalWindspeed / $totalCount );
			printf("\nAverage Wind Speed: %d knots",$averageSpeed );
		}	
		# Check to see if the total gust is provided, if true average
		# this and output an average value.
		if ($totalGust > 0){
			$averageSpeed = ($totalGust / $totalCount );
			printf("\nAverage Gust Speed: %d knots",$averageSpeed );
		}

	}

}

# Loop around and fetch all the items in the directory.
$collectionDataSet = searchDirectory();
averageStats($collectionDataSet);

printf("\n")

?>
