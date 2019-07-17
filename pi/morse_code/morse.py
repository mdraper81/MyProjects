from blinkingLed import BlinkingLed;

red_led = BlinkingLed()
red_led.addString("SOS SOS")
while (True):
    red_led.update()
