    Note: After using the Google Custom Search API for a while, I found that it does not produce results that are as consistent as those found on google.ca. I was unable to locate a reliable API service, so I changed my strategy 
    to data scraping. Although the automatic scraping now in place operates flawlessly, there could be a problem down 
    the road: if the application is used abusively, Google might block the user's IP address. Currently, this problem 
    is mitigated by changing the user_agent with each request. To guarantee that Google cannot prevent data requests 
    coming from the application in the future, a solution is required by using an service than can bypass Google bot
    recognition and captchas. Last, there is no validation in the input fields.

    Live site works now since I added a proxy to it. but keep in mind the rankng is dependable in location of the proxy used.
