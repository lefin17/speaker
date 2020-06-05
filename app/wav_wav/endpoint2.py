# endpoint two classes - one is create voice (not noice) with front info wich gives by fabric and end tail. 
# 2020-06-04 A.Poliansky
# задача - разбитие потока на содержащие не фоновую информацию файлы с адаптивным контролем шумов 
# - контроль целостности потока не задача программы
"""
2020-06-05
 
   ROADMAP
       + энергитическая функция для фрейма
       + функция определения звук ли это. Функция фабрики  # first idea of code http://asr.cs.cmu.edu/spring2012/lectures/class2.25jan/class2.datacapture.pdf
       - Начальные настройки фона 
       - функция формирования фронта заданной длинны
       - запись wav файла из объекта Endpoint на основе numpy array
       - список тестов
            - энергия фреймов (первого из буфера по всему файлу)
            - а звук ли (по фреймам по всему файлу (нулевой слой, без контроля пауз любой длины)
            
       - загрузка проетка в git
       - формирование имен файлов 
       - запись в лог файл (одно из основных и ключевых в дальнейшем - стартовый фрейм) 
       
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
    
    pauselimit = 4
    
    pauseLimitTime_ms = 100
    
    coreLimitTime_ms = 250
    
    coreLimit = 10 #counted after run
    
    tailLimit_ms = 250 # информация до объекта определяется фабрикой, информация после объекта задается объектом
    
    tailLimit = 10
    
    writefile = False # пишется ли объектом звуковой файл
    
    showProcess = True
    
    logFile = '' # внешняя аналитика
    
    startFrame = 0
    
    f #file handler
    
    def __init__(self, options): #create object if voice detected even once
        
        #if file will be createed - it will be by this path 
        self.filename = options["path"] + "/" + str(options["number_object"]) + options["filename"]  
        
        self.front = options["front"]
        
        self.freqRate = options["freqRate"]
        
        self.logFile = options["logFile"]
        
        self.startFrame = options["startFrame"] # стартовый фрейм
        
        # self.pauselimit = self.pauseLimitTime_ms / 1000 * self.freqRate / self.frameLenght во внешний объект
        
        self.coreLimit = self.coreLimitTime_ms / 1000 * self.freqRate / self.frameLenght
        
        self.tailLimit = self.tailLimit_ms / 1000 * self.freqRate / self.frameLength
        
        print ('init endpoint object ' + str(options["number_object"]))
        print ('tail limit frames ' + str(self.tailLimit))
        print ('core limit  in frames ' + str(self.coreLimit))
                
    def run(self, frame, value):
        
        self.checkpause(value)
            if self.status == 'new' and value == 1:
                self.core += frame
                self.born()
                if self.showProcess: print ('n', end='')
            elif (self.status == 'strong' or self.status == 'closing')
                self.write(frame)
                self.checkover()
                if self.showProcess: print ('S', end='') # view as run of object
            elif (self.showProcess): print ('.') #skip
    
    def write(self, arr)
        if writefile: 
            self.f.save(arr)
               
    def born(self):
        #начинаем писать файл
        if self.core.size > self.coreMinSize: 
            self.writefile = True
            self.status = 'strong'
            self.f.open # wave
            self.save.front
            self.core
            if self.showProcess: print ('B')
    
    def __del__(self): #destructor 
        if self.showProcess: print ('-')
        if self.writefile:
            self.f.close()
        #закрытие записываемого файла если таковой был
        pass
        
    def checkover(self):
        if (self.status == 'closing')
            self.writetoend -=1 #для более высокой точности нужно привязать к фреймам (5ms)
            if self.writetoend <= 0: 
                self.__del__()
        
    def checkpause(self, value):
        if value == 1: return True # если после фильтра пауз первого слоя - то завершаем объект либо на фиг его, либо финишируем 
        
        if (self.status == 'new'):
            self.__del__()
        elif (self.status == 'strong'):
            self.status = 'closing'
            self.writetoend = self.taillimit
            
            
class EndpointFabric:
    
    front = [] # numpy array with data after control frame 

    emptyframe = [] # take from start of the source file or from voice = 0
    
    frontLength = 0

    frontTimeMS = 250
    
    # частота входная для информации    
    freqRate = 44100
    
    filterPauseBuffer = []    
    
    currentFrame = 0
    
    # номер создаваемого объекта 
    objNumber = 0
    
    def __init__(self, options):
        self.name = options["name"];
    
    def frontMaker(self, frame):
        front += frame
        if front.length > frontLength:
            pass
         
    def putFilterPause(self, value):
        # если пустой филтр выполняться не должен (но и не должно быть пустого фильтра)
        for i in self.filterPauseBuffer:
            for j in self.Obj:
                self.Obj[j].run(self.filterPauseBuffer[i], value)
        self.filterPauseBuffer = []
    
    def checkFilterPause(self):
        # если фильтр переполнился - то его придется очистить, а объект если был закрыть
        if self.filterPauseBuffer.length > self.filterPauseMaxLength:
            self.putFilterPause(0)
    
    def newObj(self, frame)
        self.objectNumber +=1 
        self.Obj[self.objectNumber] = new Endpoint({front : self.front, name : 'name'})
                
    def checkObj(self, frame):
        create = True
        for i in self.Obj:
            if i.status == 'new' or i.status == 'strong': 
                create = False
                break
        if create: self.newObj(frame)
                                         
    def filterPause(self, frame, value):
        if self.Obj.length > 0 or value == 1: # если есть какие -то объекты или на входе достаточный уровень сигнала для создания объекта
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
        # если фильтр имеет критическое значение - он очищается с отправкой 0 в объект (что его начинает либо убивать, либо выводить в режим сохранения)
        # если значение 0 - растет на единицу и читается с первого элемента при выхода на 1  
        pass
        
    def firstrun(self, buf):
        #определение шума и начальных условий
        if (currentFrame == 0):
            pass
            
    def EnergyPerFrameInDecibel(self, frame):
        s = 1 * 10**-12
        res = 0
        for i in frame:
           s += (i / np.iinfo(np.int16).max)**2
        res = 10*(np.log10(s)) 
        return res         
        
    def isVoice(self, frame):
        current = self.EnergyPerSampleInDecibel(frame)
        isSpeech = False
        self.level = ((self.level * self.forgetfactor) + current) / (self.forgetfactor + 1)
        if (current < self.background):
            self.background = current
        else:
            self.background += (current - self.background) * self.adjustment
        if (self.level < self.background): level = self.background
        if (self.level - self.background > self.threshold): isSpeech = True
        return isSpeech
                     
    def run(self, buf):
        self.firstrun()
        for i in buf[::self.frameLength]: #читаем буфер по фреймам
            self.currentFrame += 1
            value = self.isVoice(frame)
            self.filterPause(frame, value)
            self.frontMaker(frame)
        pass
            
    def __del__(self):
        pass    
""" 
    1. Получить буфер с данными 
    2. Посчитать энергию звука (первый слой) - определить звук ли... 
    3. сформировать фильтр паузы (будущие показания значений) (второй слой)
    4. Если нет (живых) новых или сильных объектов создать новый объект если звук в одном из фреймов выше уровня порога (третий слой)
    5. скормить данные существующим объектам
    6. сформировать фронт (front) данных для новых объектов
--  7. почистить объекты если вымерли (они сами будут отмерать)
    8. финиш на продолжающемся буфере будущих значений
""" 

