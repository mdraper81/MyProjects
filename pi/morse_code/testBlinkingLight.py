from repeatPatternLed import RepeatPatternLed;

red_led = RepeatPatternLed(4)
red_led.setPattern([200, 200, 200, 200, 200, 2000])
while (True):
    red_led.update()