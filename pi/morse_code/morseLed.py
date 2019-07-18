from blinkingLed import BlinkingLed
import enum

class Element(enum.Enum):
    Dot = 1
    Dash = 2

class MorseLed(BlinkingLed):
    # Constants
    TIME_UNIT_MS = 150
    DOT_LENGTH_MS = TIME_UNIT_MS
    DASH_LENGTH_MS = 3 * TIME_UNIT_MS
    INTER_ELEMENT_SPACE_MS = TIME_UNIT_MS
    LETTER_GAP = 3 * TIME_UNIT_MS
    WORD_GAP = 7 * TIME_UNIT_MS

    # Constructor
    def __init__(self, pinNumber):
        BlinkingLed.__init__(self, pinNumber)
        self.MORSE_CODE_DICTIONARY = {
                "a": [Element.Dot,  Element.Dash                                           ],
                "b": [Element.Dash, Element.Dot,  Element.Dot,  Element.Dot                ],
                "c": [Element.Dash, Element.Dot,  Element.Dash, Element.Dot                ],
                "d": [Element.Dash, Element.Dot,  Element.Dot                              ],
                "e": [Element.Dot                                                          ],
                "f": [Element.Dot,  Element.Dot,  Element.Dash, Element.Dot                ],
                "g": [Element.Dash, Element.Dash, Element.Dot                              ],
                "h": [Element.Dot,  Element.Dot,  Element.Dot,  Element.Dot                ],
                "i": [Element.Dot,  Element.Dot                                            ],
                "j": [Element.Dot,  Element.Dash, Element.Dash, Element.Dash               ],
                "k": [Element.Dash, Element.Dot,  Element.Dash                             ],
                "l": [Element.Dot,  Element.Dash, Element.Dot,  Element.Dot                ],
                "m": [Element.Dash, Element.Dash                                           ],
                "n": [Element.Dash, Element.Dot                                            ],
                "o": [Element.Dash, Element.Dash, Element.Dash                             ],
                "p": [Element.Dot,  Element.Dash, Element.Dash, Element.Dot                ],
                "q": [Element.Dash, Element.Dash, Element.Dot,  Element.Dash               ],
                "r": [Element.Dot,  Element.Dash, Element.Dot                              ],
                "s": [Element.Dot,  Element.Dot,  Element.Dot                              ],
                "t": [Element.Dash                                                         ],
                "u": [Element.Dot,  Element.Dot,  Element.Dash                             ],
                "v": [Element.Dot,  Element.Dot,  Element.Dot,  Element.Dash               ],
                "w": [Element.Dot,  Element.Dash, Element.Dash                             ],
                "x": [Element.Dash, Element.Dot,  Element.Dot,  Element.Dash               ],
                "y": [Element.Dash, Element.Dot,  Element.Dash, Element.Dash               ],
                "z": [Element.Dash, Element.Dash, Element.Dot,  Element.Dot                ],
                "0": [Element.Dash, Element.Dash, Element.Dash, Element.Dash, Element.Dash ],
                "1": [Element.Dot,  Element.Dash, Element.Dash, Element.Dash, Element.Dash ],
                "2": [Element.Dot,  Element.Dot,  Element.Dash, Element.Dash, Element.Dash ],
                "3": [Element.Dot,  Element.Dot,  Element.Dot,  Element.Dash, Element.Dash ],
                "4": [Element.Dot,  Element.Dot,  Element.Dot,  Element.Dot,  Element.Dash ],
                "5": [Element.Dot,  Element.Dot,  Element.Dot,  Element.Dot,  Element.Dot  ],
                "6": [Element.Dash, Element.Dot,  Element.Dot,  Element.Dot,  Element.Dot  ],
                "7": [Element.Dash, Element.Dash, Element.Dot,  Element.Dot,  Element.Dot  ],
                "8": [Element.Dash, Element.Dash, Element.Dash, Element.Dot,  Element.Dot  ],
                "9": [Element.Dash, Element.Dash, Element.Dash, Element.Dash, Element.Dot  ]
                }

    def addString(self, stringToAdd):
        strDebug = ""
        for letter in stringToAdd:
            strDebug += self.addCharacter(letter)

        print('Blinking LED with this pattern: ' + strDebug)
    
    def addCharacter(self, newCharacter):
        # Make sure that the last time mark is either now or in the future
        if (self.currentTimeInMs > self.lastTimeMark):
            self.lastTimeMark = self.currentTimeInMs
        
        strDebug = ""

        newCharacter = newCharacter.lower()
        if (newCharacter in self.MORSE_CODE_DICTIONARY):
            strDebug += self.encodeCharacter(self.MORSE_CODE_DICTIONARY[newCharacter])
        elif (newCharacter == ' '):
            strDebug += self.encodeSpace()
        else:
            print('Failed to find letter in our dictionary')

        return strDebug

    def encodeCharacter(self, morseCodePattern):
        strDebug = ""
        encodedPattern = []
        for element in morseCodePattern:
            if (element == Element.Dot):
                strDebug += '*'
                encodedPattern.append(self.DOT_LENGTH_MS)
                encodedPattern.append(self.INTER_ELEMENT_SPACE_MS)
            elif (element == Element.Dash):
                strDebug += '-'
                encodedPattern.append(self.DASH_LENGTH_MS)
                encodedPattern.append(self.INTER_ELEMENT_SPACE_MS)
        self.addBlinkingPattern(encodedPattern)
        self.addDelay(self.LETTER_GAP - self.INTER_ELEMENT_SPACE_MS)
        strDebug += " "
        return strDebug

    def encodeSpace(self):
        self.addDelay(self.WORD_GAP - self.LETTER_GAP)
        return " "
