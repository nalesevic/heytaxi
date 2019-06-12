# **HeyTaxi**
## Description
HeyTaxi is a replacement for the current taxi booking service that relies on calling the dispatcher center which then books the taxi for a passenger. It is built to allow passengers and taxi drivers to communicate directly, without any intermediaries.<br/>
To book a taxi, passenger only needs to enter the current location and send request. Server processes the request and notifies taxi drivers who are in radius of 3 kilometers of passenger’s location. They can reply with positive or negative response. Along with positive response, estimated time of arrival is sent to the passenger, which is calculated by taxi drivers them self.<br/> Passengers can choose taxi from list of received responses. Alongside the estimated time of arrival is taxi company name and vehicle number (drivers review, type of car etc.). If passenger accepts the offer, taxi is booked and service can be cancelled within 1 minute. If declined, or another is selected, taxi driver gets negative response for their offer. <br/>
To book a taxi, passenger only needs to enter the current location and send request. Server processes the request and notifies taxi drivers who are in radius of 3 kilometers of passenger’s location. They can reply with positive or negative response. Along with positive response, estimated time of arrival is sent to the passenger.<br/> Passengers can choose taxi from list of received responses. Alongside the estimated time of arrival is taxi company name and vehicle number (drivers review, type of car etc.). If passenger accepts the offer, taxi is booked and service can be cancelled within 1 minute. If declined, or another is selected, taxi driver gets negative response for their offer. <br/>
When passenger reaches destination, payment is done by cash, and both, passenger and taxi driver, can provide feedback on each other. <br/>

## Technical aspects
### Entities: Company, Vehicle, Ride, Driver, Sessions
* Driver (first name, last name, vehicle, rating)  <br/>
* Vehicle  (brand, model, year, doors, type)  <br/>
* Company (name, email, password)  <br/>
* Ride (rideID, start point, end point)  <br/>
