I've always been curious about the prices per mile we get on our airplane tickets. For example, is a flight
to Ulan Batur actually a 'good deal' compared to a flight between Atlanta and NY? And how do prices oscillate with time? And if I want
to go on vacation during a particular week, where should I go ( in terms of the best deal on tickets)?

With these questions in mind I wanted to create a tool that allows anyone to query the best ticket prices, for multiple destinations, at the same time. Furthermore, over time it can give a good baseline of whether or not you are getting a price for a particular destination.

Most current tools concentrate on allowing you to find a flight to one specific destination and they don't give a historical perspective on the price you are getting. In other words, is this flight to this airport a good price compared to how much it cost a month ago?

If I wait a few weeks or travel during a different time might the ticket price actually go down?

Or maybe I am completely open to going anywhere just as long as I have never been there, and I want to compare the prices for many countries, cities, and regions during the same time range.

This project ideally allows users to answer these questions. First a user can choose a departure/origin point. Next the user can choose potential destination airports. Finally, the user can select a departure and return date, and click to generate the best available flight price for each of his destinations.

Then the app takes over and the client makes a request to the server, prepares a queue of requests, calculates the distance between each destination and the origin, and sends the client back a list of the destinations.

In order to improve the user experience, the client then sends individual queries for each destination, and fills in the missing information after each response. 

Potential flight solutions are retrieved using the Google Flights QPX api. After a best solution is found, the flight information is cached, both for calculating future averages, and for pulling the information directly from the database.

At this point the user can sort by the metrics retrieved, including flight price, trip duration, and cost/mile traveled. The user can also choose to start the process over or query the same origin and destinations for a different set of dates.

The site is currently hosted live at:

