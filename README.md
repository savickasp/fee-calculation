# Solution to task

Dependencies:
added dependencies only in dev mode to have dd() and dump()  functions

setup:
- download project
- composer install

Solutions flow:
- run script.php file it creates instance of FeeController
- then call methed getOperations(). It gets all operatiosn from file and add them to controller parameters
- if error found in previuos method it prints out error and exit
- if there is no error thten it calls calculateFees() and return array 
- array is printed to stdout
- then exit

created seperate class CsvReader to read and get content of csv file
