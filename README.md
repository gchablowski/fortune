Fortune Rest API
========================

Some Rest Api developed with symphony 2.
The aim was to create a service where some client register and receive an email with a new quote every day.

It use :
FOSOAuthServerBundle
FosRestBundle
8p/GuzzleBundle
HipMandrillBundle

OAuth 2 is use with client_credentials grant to give a token to the front-end client (/oauth/v2/token/client_id=XXX&client_secret=XXX&grant=client_credentials).

A bundle call Fortune Bundle which define :
3 command line :
fortune:oauth-server:client:create (to create some oath client)
fortune:getQuote (to get a quote from a service access by GuzzleBundle)
fortune:setQuote (to register a new quote on the database)
fortune:SendFortunes (to send the fortune mail to the Mandrill service)

The controller define 4 routes :
/quotes (GET the list of all the quote in database order by date accessible)
/email/register (to save an email with a POST)
/email/activate/:token (to activate mail in database with PUT)
/email/desactivate/:token (to desactivate mail in database with PUT)

you may find the angular-js client here : https://github.com/gchablowski/fortune-angular


Created by Gérald Chablowski.
