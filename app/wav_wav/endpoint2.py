import numpy as np
import math 
import wave 

#, struct

import scipy.io
# endpoint two classes - one is create voice (not noice) with front info wich gives by fabric and end tail. 
# 2020-06-04 A.Poliansky
# задача - разбитие потока на содержащие не фоновую информацию файлы с адаптивным контролем шумов 
# - контроль целостности потока не задача программы
"""
2020-06-09 Отладка разбиения, почти все хорошо, но осталось 10% которые отъедают 90% времени
    - отложенное удаление объекта (вне цикла работы по объектам)
    - добавление текущей позиции в создание объекта
     
2020-06-08
    ROADMAP 
        - отладка и тестирование 
        - финиш процесса в конце потока или файла
        
2020-06-05
 
   ROADMAP
       + энергитическая функция для фрейма
       + функция определения звук ли это. Функция фабрики  # first idea of code http://asr.cs.cmu.edu/spring2012/lectures/class2.25jan/class2.datacapture.pdf
       + Начальные настройки фона 
       + функция формирования фронта заданной длинны
       + запись wav файла из объекта Endpoint на основе numpy array
       + список тестов
            + энергия фреймов (первого из буфера по всему файлу)
            + а звук ли (по фреймам по всему файлу (нулевой слой, без контроля пауз любой длины)
            
       + загрузка проетка в git
       + формирование имен файлов 
       + запись в лог файл (одно из основных и ключевых в дальнейшем - стартовый фрейм) 
       
"""
class Endpoint:
    # класс работает над записью фрейма в файл если объект достиг зрелости (core limit time ms)
    
    status = 'new' # start new object with this status next is strong or dead 
				 # when status changed from new to strong - start to write file - keep tail first and after long pause - closing by some time 
    
    front = []    # array with nympy data of start voice in new object
    
    frameLenght = 256 
	
    core = []    # core - is main of array
    
    pause = 0    # if pause is more then limit (in buffer) change status 
    
    freqRate = 44100
    
    name = 'voice'
    
    logfile = '';
    
    pauselimit = 4
    
    pauseLimitTime_ms = 100
    
    coreLimitTime_ms = 250
    
    coreLimit = 10 #counted after run
    
    tailLimit_ms = 250 # информация до объекта определяется фабрикой, информация после объекта задается объектом
    
    tailLimit = 10
    
    writefile = False # пишется ли объектом звуковой файл
    
    showProcess = True
    
    frameLength = 256
    
    coreMinSize = 0
    
    coreMinSizeMS = 250
    
    logFile = '' # внешняя аналитика (может быть уже handler)
    
    startFrame = 0
    
    id = 0
    
    wave_file = None
    #file handler wave_file
    
    cb = None
    def __init__(self, options, cb): #create object if voice detected even once
        
        #if file will be createed - it will be by this path
        self.cb = cb #call back function 
        
        self.id = options.get("id", self.id) 
        self.filename = options.get("path", "./dst") + "/" + str(self.id) + options.get("name", "wav1") + ".wav"
          
        
        self.frameLength = options.get("frameLength", self.frameLength)
        
        self.front = options.get("front", [])
        
        self.freqRate = options.get("freqRate", 44100)
        
        self.coreMinSize = self.coreMinSizeMS / 1000 * self.freqRate / self.frameLength
        
        self.logFile = options.get("logFile", "ep2.data.log")
        
        self.startFrame = options.get("startFrame", 0) # стартовый фрейм
        
        # self.pauselimit = self.pauseLimitTime_ms / 1000 * self.freqRate / self.frameLenght во внешний объект
        
        self.coreLimit = self.coreLimitTime_ms / 1000 * self.freqRate / self.frameLenght
        
        self.tailLimit = self.tailLimit_ms / 1000 * self.freqRate / self.frameLength
        
        print ('init endpoint object id {:2}'.format(self.id))
        print ('tail limit frames ' + str(self.tailLimit))
        print ('core limit  in frames ' + str(self.coreLimit))
                
    def run(self, frame, value):
        
        self.checkpause(value)
        if self.status == 'new' and value == 1:
            self.core.append(frame)
            self.born()
            if self.showProcess: print ('n', end='')
        elif (self.status == 'strong' or self.status == 'closing'):
            self.write(frame)
            self.checkover()
            if self.showProcess: print ('S', end='') # view as run of object
        elif (self.showProcess): print ('.') #skip
    
    
    def write(self, arr):
        if self.writefile:
             
            data = arr.tobytes()
            # data = struct.pack('%sf' % len(arr), *arr)   
           # print (data)
            
           # exit()     
            self.wave_file.writeframes(data) # https://docs.python.org/2/library/wave.html
    
    def writeFront(self): # https://stackoverflow.com/questions/9940859/fastest-way-to-pack-a-list-of-floats-into-bytes-in-python
        arr = self.front.flatten()
        data = arr.tobytes()
        # data = struct.pack('%sf' % len(arr), *arr)
        self.wave_file.writeframes(data)
            
            
    def writeCore(self):
        arr = np.array(self.core)  # .flatten()
        #print (arr)
        #exit()
        #data = struct.pack('%sf' % len(arr), *arr)
        data = arr.tobytes()
        self.wave_file.writeframes(data)
                
            
    def born(self):
        #начинаем писать файл
        if np.size(self.core) > self.coreLimit: 
            self.writefile = True
            self.status = 'strong'
            self.wave_file = wave.open(self.filename, "w") # wave
            self.wave_file.setnchannels(1)
            self.wave_file.setsampwidth(2)
            self.wave_file.setframerate(self.freqRate)
            self.writeFront() 
            self.writeCore()           
            if self.showProcess: print ('B')
    
    def closeObject(self):
        if self.showProcess: print ('co-{:0}'.format(self.id))
        if self.writefile:
            self.wave_file.close()
            
            with open(self.logFile, 'a') as f:
                f.write(str(self.startFrame) + ';' + self.filename+ "\n")
            self.writefile = False
        self.cb(self.id) #callback       
        self.__del__()
        #закрытие записываемого файла если таковой был
        pass
             
    def __del__(self): #destructor 
        print ("-")       
        
    def checkover(self):
        if (self.status == 'closing'):
            self.writetoend -=1 #для более высокой точности нужно привязать к фреймам (5ms)
            if self.writetoend <= 0: 
                self.closeObject()
        
    def checkpause(self, value):
        if value == 1: return True # если после фильтра пауз первого слоя - то завершаем объект либо на фиг его, либо финишируем 
        
        if (self.status == 'new'):
            self.closeObject()
        elif (self.status == 'strong'):
            self.status = 'closing'
            self.writetoend = self.tailLimit
            
            
class EndpointFabric:
    
    front = np.array([]) # numpy array with data after control frame 

    emptyframe = [] # take from start of the source file or from voice = 0
    
    frontLength = 0 # count in init function 
    
    frameLength = 256
    
    forgetfactor = 1.1
    
    adjustment = 0.05
    
    threshold = 20    
    
    frontTimeMS = 250
    
    # частота входная для информации    
    freqRate = 44100
    
    filterPauseBuffer = [] 
    
    filterPauseMaxLength = 0   
    
    filterPauseMS = 100
    
    currentFrame = 0
    
    Obj = {} # объекты с записью элемента звука
    
    # номер создаваемого объекта 
    objectNumber = 0
    
    def __init__(self, options):
        self.name = options.get("name", "voice");
        self.frontLength = round(self.frontTimeMS / 1000 * self.freqRate / self.frameLength)
        self.filterPauseMaxLength = round(self.filterPauseMS / 1000 * self.freqRate / self.frameLength)    
        print ('frontLength {:3}'.format(self.frontLength))
        print ('filterPause Length: {:3}'.format(self.filterPauseMaxLength))
        
       # exit(1)

    def frontMaker(self, frame):
        
        if (self.front.ndim and self.front.size) == 0:
            self.front = np.array([frame])
        elif (np.size(frame) == self.frameLength) :
            self.front = np.vstack([self.front, frame])
        else:
            return False # Last frame              
        if np.size(self.front, 0) > self.frontLength:
            self.front = np.delete(self.front, 0, axis=0) # удаляем первую строку из матрицы фронта 
            
            
    def putFilterPause(self, value):
        # если пустой филтр выполняться не должен (но и не должно быть пустого фильтра)
        for i in range(np.size(self.filterPauseBuffer, 0)):
            for j in self.Obj:
                frame = self.filterPauseBuffer[i]
              #  print ('frame: ', end='')
              #  print (frame)
                # print ('Length of object')
                print (len(self.Obj), end='')
                print (self.Obj[j])
               # print ('current Object {:3}'.format(j))
                if (isinstance(self.Obj[j], Endpoint)):
                    print ('r')
                    self.Obj[j].run(frame, value)
        self.filterPauseBuffer = []
    

    def checkFilterPause(self):
        # если фильтр переполнился - то его придется очистить, а объект если был закрыть
        if np.size(self.filterPauseBuffer, 0) > self.filterPauseMaxLength:
            self.putFilterPause(0)
    

    def newObj(self, frame):
        self.objectNumber +=1 
        print ('try to create New object {:3}'.format(self.objectNumber))
        self.Obj.update({self.objectNumber : Endpoint(
            {"front" : self.front, 
              "name" : self.name,
              "freqRate": self.freqRate,
              "id": self.objectNumber
              }, self.closeObj)})
              
    def closeObj (self, id):
        # close object wich is closed 
        print ('Try to remove Object {:3}'.format(id))
        print (self.Obj)        
        # self.Obj.remove(id)       
        del self.Obj[id]
        #exit()
        #self.Obj.remove(id)            
    
    def checkObj(self, frame):
        create = True
        for i in self.Obj:
            if self.Obj[i].status == 'new' or self.Obj[i].status == 'strong': 
                create = False
                break
        if create: self.newObj(frame)
                                         
    def filterPause(self, frame, value):
        if len(self.Obj) > 0 or value == 1: # если есть какие -то объекты или на входе достаточный уровень сигнала для создания объекта
            self.filterPauseBuffer.append(frame)
   
        if value == 0: 
            self.checkFilterPause()
        elif value == 1:
            self.checkObj(frame) # создавать или нет объект
            self.putFilterPause(1)
                  
        # если голос - можно пускать в обработку сразу без тормоза и сдвига
        # может вызывать эффект паузы, а также вызывать больше одного раза объекты обработки или не вызывать их вообще
        # может вызывать задержку на несколько фреймов
        # как-то хитро взаимодействует с front'ом (либо занимает большой объем памяти, если для каждого фрейма сохранять свой front либо собираем в зависимости от фрейма)
        # никак не взаимодействует с фронтом - либо убивает объект, либо продолжает его обработку пуская информацию в него с пометкой не пауза
        # перед тем как удалить очередной элемент фильтра - нужно обработать существующие объекты и либо это его закрытие, либо продолжение выполненное путем обработки всего фильтра
        # если филь тр имеет критическое значение - он очищается с отправкой 0 в объект (что его начинает либо убивать, либо выводить в режим сохранения)
        # если значение 0 - растет на единицу и читается с первого элемента при выхода на 1  
        pass
        
    def firstrun(self, buf):
        #определение шума и начальных условий
        if (self.currentFrame != 0): return False
        size = np.size(buf)
        element = math.ceil(np.size(buf) / self.frameLength)
        for i in range(element): 
            start = i * self.frameLength
            frame = buf[start::self.frameLength]            
            current = []
            current.append(self.EnergyPerFrameInDecibel(frame))
        self.background = np.mean(current)
        self.level = self.background
            
            
    def EnergyPerFrameInDecibel(self, frame):
        s = 1 * 10**-12
        res = 0
        for i in frame:
           s += (i / np.iinfo(np.int16).max)**2
        res = 10*(np.log10(s)) 
        return res         
        

    def isVoice(self, frame):
        current = self.EnergyPerFrameInDecibel(frame)
        isSpeech = False
        self.level = ((self.level * self.forgetfactor) + current) / (self.forgetfactor + 1)
        if (current < self.background):
            self.background = current
        else:
            self.background += (current - self.background) * self.adjustment
        if (self.level < self.background): self.level = self.background
        if (self.level - self.background > self.threshold): isSpeech = True
        return isSpeech

                     
    def run(self, buf):
        # Основная функция 
        self.firstrun(buf)
        size = np.size(buf)
        element = math.ceil(size / self.frameLength)
        
        for i in range(element): #читаем буфер по фреймам
            start = i * self.frameLength
            end = (i + 1) * self.frameLength
            frame = buf[start:end]

            self.currentFrame += 1
            value = self.isVoice(frame)
            self.filterPause(frame, value)
            self.frontMaker(frame)
        

    
    def test_energy(self, buf):
        # print (buf)
        size = np.size(buf)
        element = math.ceil(size / self.frameLength)
        print ('size  - {:2}, element, {:4}'.format(size, element))        
        for i in range(element): #читаем буфер по фреймам
            start = i * self.frameLength
            end = (i + 1) * self.frameLength
            frame = buf[start:end]
            current = self.EnergyPerFrameInDecibel(frame)
            print ('frame : {:0}, current : {:3.3f}'.format(i, current))
            
    def test_isVoice(self, buf):
        #тест создания и работы фронта записи для каждого фрейма
        size = np.size(buf)
        element = math.ceil(np.size(buf) / self.frameLength)
        self.firstrun(buf)
        for i in range(element): #читаем буфер по фреймам
            start = i * self.frameLength
            end = (i + 1) * self.frameLength
            frame = buf[start:end]
            self.currentFrame += 1
            value = self.isVoice(frame)
           # print (self.background)
            print(int(value), end='')
        
    def test_front(self, buf):
        #тест создания и работы фронта записи для каждого фрейма
        size = np.size(buf)
        element = math.ceil(np.size(buf) / self.frameLength)

        for i in range(element): #читаем буфер по фреймам
            start = i * self.frameLength
            end = (i + 1) * self.frameLength
            frame = buf[start:end]
        #    print (self.frameLength)
            # print (frame)
            self.currentFrame += 1
            self.frontMaker(frame)
      #  print(np.size(self.front, 0))
      #  print (self.front)
        
            
    def __del__(self):
        pass    
""" 
++  1. Получить буфер с данными 
++  2. Посчитать энергию звука (первый слой) - определить звук ли... 
++  3. сформировать фильтр паузы (будущие показания значений) (второй слой)
    4. Если нет (живых) новых или сильных объектов создать новый объект если звук в одном из фреймов выше уровня порога (третий слой)
++  5. скормить данные существующим объектам
++  6. сформировать фронт (front) данных для новых объектов
--  7. почистить объекты если вымерли (они сами будут отмерать)
    8. финиш на продолжающемся буфере будущих значений
""" 

