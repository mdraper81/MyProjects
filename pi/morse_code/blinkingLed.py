import queue
import time

class BlinkingLed:
    # Constants
    TIME_UNIT_MS = 50
    DOT_LENGTH_MS = TIME_UNIT_MS
    DASH_LENGTH_MS = 3 * TIME_UNIT_MS
    INTER_ELEMENT_SPACE_MS = TIME_UNIT_MS
    LETTER_GAP = 3 * TIME_UNIT_MS
    WORD_GAP = 7 * TIME_UNIT_MS
    NO_PENDING_TOGGLE_MARK = -1
    
    # Constructor
    def __init__(self):
        self.updateQueue = queue.Queue(maxsize=0)
        self.isLedOn = False
        self.currentTimeInMs = int(time.time()*1000)
        self.lastTimeMark = 0
        self.upcomingToggleMark = self.NO_PENDING_TOGGLE_MARK
    
    def addString(self, stringToAdd):
        for letter in stringToAdd:
            self.addCharacter(letter)
    
    def addCharacter(self, newCharacter):
        # Make sure that the last time mark is either now or in the future
        if (self.currentTimeInMs > self.lastTimeMark):
            self.lastTimeMark = self.currentTimeInMs
        
        if (newCharacter == 's' or newCharacter == 'S'):
            self.insertSymbol(self.DOT_LENGTH_MS)
            self.insertSymbol(self.DOT_LENGTH_MS)
            self.insertSymbol(self.DOT_LENGTH_MS)
            self.insertLetterSpace()
        elif (newCharacter == 'o' or newCharacter == 'O'):
            self.insertSymbol(self.DASH_LENGTH_MS)
            self.insertSymbol(self.DASH_LENGTH_MS)
            self.insertSymbol(self.DASH_LENGTH_MS)
            self.insertLetterSpace()
        elif (newCharacter == ' '):
            self.insertWordSpace()
        
    def insertSymbol(self, duration):
        # Toggle LED on
        self.updateQueue.put(self.lastTimeMark)
        
        # Delay for the duration of the symbol 
        self.lastTimeMark = self.lastTimeMark + duration
        
        # Toggle LED off
        self.updateQueue.put(self.lastTimeMark)
        
        # Delay for the duration of one space so that we are ready for the next character
        self.lastTimeMark = self.lastTimeMark + self.INTER_ELEMENT_SPACE_MS
    
    def insertLetterSpace(self):
        # Delay for the duration of one letter gap... note we have to remove the inter-element space from the last element
        self.lastTimeMark = self.lastTimeMark + self.LETTER_GAP - self.INTER_ELEMENT_SPACE_MS
        
    def insertWordSpace(self):
        # Delay for the duration of one word gap... note we have to remove the letter gap from the last letter
        self.lastTimeMark = self.lastTimeMark + self.WORD_GAP - self.LETTER_GAP
    
    # Called every frame to update the blinking LED
    def update(self):
        self.currentTimeInMs = int(time.time()*1000)
        
        if (self.upcomingToggleMark != self.NO_PENDING_TOGGLE_MARK): # We are waiting on a tick
            if (self.currentTimeInMs > self.upcomingToggleMark): # And it is time for that tick
                if (self.isLedOn):
                    print('off at time: ' + str(self.currentTimeInMs))
                else:
                    print('on  at time: ' + str(self.currentTimeInMs))
                
                # Toggle LED and reset the upcoming toggle mark indicator
                self.isLedOn = not self.isLedOn
                self.upcomingToggleMark = self.NO_PENDING_TOGGLE_MARK
        elif (not self.updateQueue.empty()): # We are not waiting on a tick, let's check if one is queued
            self.upcomingToggleMark = self.updateQueue.get()