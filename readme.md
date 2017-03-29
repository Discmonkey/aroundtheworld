This project attemps to solve the problem of comparing flight prices across multiple locations, dates, and time periods.

Most current tools that I have seen concentrate on allowing you to find a flight to one specific destination, and they don't give a historical perspective on the price you are getting. So is this flight China a good price compared to how much it cost a month ago? If I wait a few weeks might the ticket price actually go down? 

Or, maybe, what I really want to do is to visit Asia,and so maybe I want to compare the prices across Japan, South Korea, Taiwan, China, Thailand, and Malaysia at the same time?

This project ideally allows users to answer these questions. First a user can choose a departure/origin point. Next the user can choose potential destination airports. Finally, the user can select a departure and return date, click to generate the best available flight price for each of his destinations. 

At this point the client makes a request to the server, prepares a queue of requests, calculates the distance between each destination and the origin, and sends the client a list of the destinations.

In order to improve the user experience, the client then sends individual queries for each destination, and fills in the missing information after each response. 

Potential flight solutions are retrieved using the Google Flights QPX api. After a best solution is found, the flight information is cached, both for calculating future averages, and for pulling the information directly from the database. In case, another user makes the same flight request in the near future. 

At this point the user can sort by the metrics retrieved, including flight price, trip duration, and cost/mile traveled. The user can also choose to start the process over or query for a different set of dates. 
