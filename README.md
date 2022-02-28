# Custom actions

This module provides the functionality to schedule multiple downtimes by Icinga-API-Calls composed by host and service filters with one single form. To date you had to schedule downtimes by selecting the hosts/service individually or by creating filters and selecting them then. Now you only have to choose all the predefined filters you want, fill the form with the information of the downtime (start- and end time, type etc.) and your downtimes are scheduled. Currently its only possible to schedule downtimes, but planned to be also able to make acknowledgements, comments etc. (in theory in could execute every action the API is capable of).

## Motivation
It is often annoying to schedule downtimes, especially when you have to set a large amount of different downtimes and you have to schedule them in regular time periods like every weekend but the downtimes differ slightly from one another and you can not use a single filter containing all objects.

## Documentation 
* [Installation](doc/02-Installation.md)


## Author

* **Dominik Gramegna** - *Initial work*

## License

* See the [LICENSE](LICENSE) for details
