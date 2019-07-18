from blinkingLed import BlinkingLed

class RepeatPatternLed(BlinkingLed):
    # Constructor
    def __init__(self, pinNumber):
        BlinkingLed.__init__(self, pinNumber)
        self.pattern = []
    
    def setPattern(self, patternToBlink):
        self.pattern = patternToBlink
        self.stopAndClearQueue()
        self.restartPattern()
    
    def restartPattern(self):
        if (len(self.pattern) > 0):
            self.addBlinkingPattern(self.pattern)
    
    def stopBlinking(self):
        self.pattern = []
        self.stopAndClearQueue()
    
    def update(self):
        BlinkingLed.update(self)
        
        if (self.isIdle()):
            self.restartPattern()