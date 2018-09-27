import urllib.request as request
import datetime, os
# ----------------------------------------
#		Setup the key data items.
# ----------------------------------------
months =\
['Jan','Feb','Mar','Apr','May','Jun','Jul',\
'Aug','Sep','Oct','Nov','Dec']
monthsFull =\
['January','February','March','April','May',\
'June','July','August','September','October',\
'November','December']
# ---------------------------------------
#		Loop around the searchDates. (Find valid days.
# ---------------------------------------
def searchDates(monthNumber = 9,maximumDate = 22):
	assert monthNumber > 0 and monthNumber < 13 , 'Months out of range'
	days = []
	for count in range(1,35):
		try:
			datetime.date(2018,monthNumber,count)
			days.append(count)
			#print("Valid: ", count)
		except:
			break
	return days
# ---------------------------------------
#	Function - requestDataItem
#		Request the data item.
# ---------------------------------------
def requestDataItem(date=None):
	urlRequestHandle = 'http://www.bramblemet.co.uk/archive/2018/September/CSV/Bra'+\
											date+'Sep2018.csv'
	print("Request: ", urlRequestHandle)
	responseHandle = request.urlopen(urlRequestHandle)
	responseData = responseHandle.read().decode('utf-8')
	print("Data: ", responseData)
	return responseData

def responseToFile():
	if os.path.isdir('./data')==False:
		os.mkdir('./data')
	if os.path.isdir('./dataCSV')==False:
		os.mkdir('./dataCSV')
	itemCount = len(os.listdir('./dataCSV/'))+1

	for counter in range(1,22):
		dateString = None
		if counter < 10:
			dateString = '0'+str(counter)
		else:
			dateString = str(counter)
		responseData = requestDataItem(date=dateString)
		fileHandler = open('./dataCSV/response'+\
												str(itemCount)+"_"+str(counter)+".csv",\
												'w',encoding="utf-8")
		fileHandler.write(responseData)
		fileHandler.close()

responseToFile()

#http://www.bramblemet.co.uk/archive/2018/January/CSV/Bra11Jan2018.csv


