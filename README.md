# FPP-GPIO-Toggle
This plugin allows you to toggle regular FPP GPIO Channel Outputs

    1. Setup Your GPIO Output under Channel Outputs > Other
    2. Return to Plugin page and there should be buttons to toggle the state of the GPIO Output, 
       it will toggle from the current setting to either LOW or HIGH depending on the current GPIO value
    3. Optionally Set the toggle time (delay between on and off), default 2000ms ( 2 seconds )

I created this because I had a NC relay intercepting a the trigger for 240v contactor and needed a easy way to Open and Close it so I can toggle power on that output. 
I run a remote show (15 minutes drive) and had a issue in 2016 where I sometimes needed to power cycle an output on numerous occasions to restart all my controllers / FPP salve. 

This can be used for any other situation where you want to manually toggle a GPIO output via the web interface for whatever reason

**Settings Page**

![Alt text](/images/settings_page.png?raw=true "Settings Page")