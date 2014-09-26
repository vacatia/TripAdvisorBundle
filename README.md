TripAdvisorBundle
=================

Symfony2 wrapper for the TripAdvisor public API

Installation
------------

Add the bundle to your `composer.json` (to be edited soon)

First of all, configure a connection in the doctrine DBAL:

Configuration Reference
-----------------------

```yml
vacatia_trip_advisor:
    key:                  ~ # Required
    cache:
        service:              ~ # Required; Must be a doctrine cache provider
        lifetime:             86400
```
