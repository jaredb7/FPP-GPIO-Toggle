# FPP-GPIO-Toggle
This plugin allows you to toggle regular FPP GPIO Channel Outputs

1. Setup Your GPIO Output under Channel Outputs > Other
2. Return to Plugin page and there should be buttons to toggle the state of the GPIO Output, it will toggle from the current setting to either LOW or HIGH depending on the current GPIO value

I created this because I had a NC relay hooked 240v Contactor and needed a way to Open and close it so I can toggle power on that output. 
I run a remote show (20mins drive) and had a issue in 2016 where I sometimes needed to power cycle an output on numerous occasions. 

This can be used for any other problem or situation where you want to manually toggle a GPIO output via the web interface

Toggling is only available on RPi since I use wiringPi's gpio read to find out the current state of an output when toggling.
