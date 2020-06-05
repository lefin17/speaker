# first idea of code http://asr.cs.cmu.edu/spring2012/lectures/class2.25jan/class2.datacapture.pdf

# adaptive voice (sound) detection, with energy detection
import numpy as np
import sys
class Endpoint:

    forgetfactor = 1 # more for complicated backgrond noice ff => 1
    adjustment = 0.05 # 
    threshold = -26 # db уровень больше которого сигнал считается несущим
    frequencyRate = 44100 
    isSpeechA = [] # array speech block before smooth
    isSpeechB = [] # array speech block after smooth
    frameLenght = 2048 #
    minSpeechTime = 250 # time in ms (ms) 
    minPauseTime = 100 # min pause (ms)
    nSpeech = 1
    nPause = 1
    frameLength = 256
    addSpeech = 1 #number of frames of audio to add to the end of speech and before start
    nBackground = 10 #number of first blocks to detect background energy
    addSpeechTime = 250 # add time to start and end points in ms 
#     audioblock - np array of audioframes to determine background level
#    audioblock = [] # N of audioblocks, 
    
    def __init__(self, threshold=-26, frequencyRate=44100, frameLength=2048):
        self.threshold = threshold
        self.frameLenght = frameLength
        self.frequencyRate = frequencyRate
	
        self.nPause = round((self.frequencyRate / 1000 * self.minPauseTime) / self.frameLength)   # pause in frames
        self.nSpeech = round((self.frequencyRate / 1000 * self.minSpeechTime) / self.frameLength)   # pause in frames
        self.addSpeech = round((self.frequencyRate / 1000 * self.addSpeechTime) / self.frameLength)
        
	
	
	def takeBuffer(slef, bufferA)
        	
        self.bufferA[2] = self.buffer[1]
        self.bufferA[1] = self.buffer[0]
        self.bufferA[0] = bufferA
        
        
    def aStep(self, audioblock):
	#start voice detection by addaptive background method by energy of frame 
        N = round(audioblock.size / self.frameLenght)
        for i in range(N):
            start = i * self.frameLength
            end = (i + 1)  * self.frameLength
        # isSpeech[i] = self.classifyFrame(audioblock[start:end])
            isSpeech.append(self.classifyFrame(audioblock[start:end]))
	
    def removeGaps(self, isSpeechA):
        k = 0
        smooth = False
        nPause = self.nPause
        isSpeechB = isSpeechA
	   # remove small pauses 
        for i in range(isSpeechA.size):
            if isSpeechA[i]: k, smooth = i, False
            if not smooth and not isSpeechA[i] and k + nPause >= i:
                for j in isSpeechA[i:k + nPause]:
                    if isSpeechA[j]: 
                        smooth = True
                        break
		    
            if smooth: isSpeechB[i] = True
	       
        return isSpeechB

    def removeSmallVoice(self, isSpeechA):
	#remove faked voice 
    	isSpeechB = isSpeechA
    	nSpeech = self.nSpeech
    	k = 0
    	smooth = False
    	for i in range(count(isSpeechA)):
    	    if not isSpeechA[i]: k, smooth = i, False    
    	    if not smooth and isSpeechA[i] and k + nSpeech >= i:
    	        for j in isSpeechA[i:k + nSpeech]:
                    if not isSpeechA[j]: 
                        smooth = True
                        break
    			
    	    if smooth: isSpeechB[i] = False
    	return isSpeechB
		
    def addVoiceTime(self, isSpeechA):
        #add labeling of frame with speech marker 
        isSpeechB = isSpeechA
        addSpeech = self.addSpeech
	# add time to speech before start and after end
        smooth = False
        for i in range(count(isSpeechA)):
            if isSpeechA[i]: k, before, after = i, False, True
            if after and not isSpeechA[i]: isSpeechB[i] = True 
            if not before and not isSpeechA[i] and i + addSpeech < count(isSpeechA) and isSpeechA[i+addSpeech]: before = True
		   
            if before: isSpeechB[i] = True
		   
        return isSpeechB
 		
    def bStep(self):
       # if pause less then self.minPause - that is not pause
	   # if speech less then self.min Speech - that is not speech (but after pause removed
        # after all - add the frames before start and after end
        isSpeechB = removeGaps(self.isSpeechA)
        isSpeechB = removeSmallVoice(isSpeechB)
        isSpeechB = addVoiceTime(isSpeechB)	
        self.isSpeechB = isSpeechB		
			
		         
    def EnergyPerSampleInDecibel(self, audioframe):
        s = 1 * 10**-12
        res = 0
 #       print (audioframe)
        for i in audioframe:
           s += (i / np.iinfo(np.int16).max)**2
          # print(i)
#        if (s > 0): 
        res = 10*(np.log10(s)) # + np.log(1/(np.iinfo(np.int16).max**2) * count(audioframe)))
        return res

    def classifyFrame(self, audioframe):
        current = self.EnergyPerSampleInDecibel(audioframe)
        isSpeech = False
        self.level = ((self.level * self.forgetfactor) + current) / (self.forgetfactor + 1)
        if (current < self.background):
            self.background = current
        else:
            self.background += (current - self.background) * self.adjustment
        if (self.level < self.background): level = self.background
        if (self.level - self.background > self.threshold): isSpeech = True
        return isSpeech
	
    def getBackground(self, audioblock):
  
	    #get avarage energy level of N first blocks when start determine  
        N = round(audioblock.size / self.frameLenght)
        if N > self.nBackground: N = self.nBackground
        b = [] 
        for i in range(N):
            start = i * self.frameLength
            end = (i + 1)  * self.frameLength
            #print (start)
            #print (end)
            #print (audioblock)
            #sys.exit(2)
             
        # isSpeech[i] = self.classifyFrame(audioblock[start:end])
            b.append(self.EnergyPerSampleInDecibel(audioblock[start:end]))
            # print(b)
        background = np.mean(b)
        print ('Background {}'.format(background))
        return background    
    	
if __name__  == "__main__":
    pass
    #test the module 
    # get file -> get background -> classify Frames -> smooth...
    #  	         