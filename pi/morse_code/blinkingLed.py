import array
import gpiozero
import queue
import time

class BlinkingLed:
    # Constants
    NO_PENDING_TOGGLE_MARK = -1
    ENABLE_DEBUG_PRINTS = True
    
    # Constructor
    def __init__(self, pinNumber):
        self.updateQueue = queue.Queue(maxsize=0)
        self.isLedOn = False
        self.currentTimeInMs = int(time.time()*1000)
        self.lastTimeMark = 0
        self.upcomingToggleMark = self.NO_PENDING_TOGGLE_MARK
        self.led = gpiozero.LED(pinNumber)
    
    def ensureValidLastTimeMark(self):
        self.currentTimeInMs = int(time.time()*1000)
        
        if (self.currentTimeInMs > self.lastTimeMark):
            self.lastTimeMark = self.currentTimeInMs
    
    def addBlinkingPattern(self, pattern):
        self.ensureValidLastTimeMark()
        
        # Go through the array of durations and queue them up for the LED
        for duration in pattern:
            # Toggle LED
            self.updateQueue.put(self.lastTimeMark)
            
            # Delay for the given duration
            self.lastTimeMark = self.lastTimeMark + duration
    
    def addDelay(self, duration):
        self.ensureValidLastTimeMark()
        self.lastTimeMark = self.lastTimeMark + duration
    
    def stopAndClearQueue(self):
        if (self.isLedOn):
            self.led.off()
            self.isLedOn = False
        
        self.lastTimeMark = self.currentTimeInMs
        self.upcomingToggleMark = self.NO_PENDING_TOGGLE_MARK
        while (not self.updateQueue.empty()):
            self.updateQueue.get()
    
    def updateLED(self):
        if (self.isLedOn):
            self.led.on()
        else:
            self.led.off()
    
    def isIdle(self):
        if (self.upcomingToggleMark == self.NO_PENDING_TOGGLE_MARK and self.updateQueue.empty()):
            return True
        
        return False
    
    # Called every frame to update the blinking LED
    def update(self):
        self.currentTimeInMs = int(time.time()*1000)
        
        # If we are not processing a character already check the queue to see if there is one to process
        if (self.upcomingToggleMark == self.NO_PENDING_TOGGLE_MARK and not self.updateQueue.empty()):
            self.upcomingToggleMark = self.updateQueue.get()
        
        if (self.upcomingToggleMark != self.NO_PENDING_TOGGLE_MARK): # We are waiting on a tick
            if (self.currentTimeInMs > self.upcomingToggleMark): # And it is time for that tick
                if (self.ENABLE_DEBUG_PRINTS):
                    strDebug = 'Turning LED '
                    if (self.isLedOn):
                        strDebug += 'off'
                    else:
                        strDebug += ' on'
                    strDebug += ' at time ' + str(self.currentTimeInMs)
                    print(strDebug)
                
                # Toggle LED and reset the upcoming toggle mark indicator
                self.isLedOn = not self.isLedOn
                self.upcomingToggleMark = self.NO_PENDING_TOGGLE_MARK
        
        self.updateLED()