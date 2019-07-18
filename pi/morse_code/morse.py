from morseLed import MorseLed;

red_led = MorseLed(4)
while (True):
    red_led.update()

    if (red_led.isIdle()):
        stringToEncode = input("Enter a sentence to encode: ")
        red_led.addString(stringToEncode)
