<?PHP

#include <EEPROM.h> // ep rome library {to store data}
#include <Adafruit_Fingerprint.h> // fingerprint data {to use fingerprint sensor}
int buzz_pin = 4; // D4 for buzzer
const int relay_pin = 5; // D5 for led/relay
SoftwareSerial mySerial(2, 3); // D2-rx-green_wire, D3-tx-blue_wire
Adafruit_Fingerprint finger = Adafruit_Fingerprint(&mySerial);
int storedState; // for ep rom to store led/relay state on D5
int epromstatus; // to active and deactive eprom itself

// for fingerprint touch duration count
unsigned long pressStartTime = 0; 
unsigned long pressDuration = 0;



void setup()
{
  pinMode(relay_pin,OUTPUT);
  pinMode(buzz_pin,OUTPUT);

  epromstatus = EEPROM.read(0); // reads eprom status and store in variable
  if(epromstatus==HIGH){ // if epromsstatus or eprom itself is activated
    storedState = EEPROM.read(1); //check D6 eprom and store in storedState
    if(storedState==HIGH){digitalWrite(relay_pin,LOW);} //
    else{digitalWrite(relay_pin,HIGH);}
  }else{
    // EEPROM.write(0, HIGH);
  }
  

  Serial.begin(9600);
  while (!Serial);  // For Yun/Leo/Micro/Zero/...
  delay(100);
  Serial.println("\nAdafruit finger detect test");

  // set the data rate for the sensor serial port
  finger.begin(115200);
  delay(5);
  // check if fingerprint sensor is connected
  if(finger.verifyPassword()){Serial.println("Found fingerprint sensor!");} 
  else{Serial.println("Did not find fingerprint sensor :(");while(1){delay(1);}}

  Serial.println(F("Reading sensor parameters"));
  finger.getParameters(); 
  // Serial.print(F("Status: 0x")); Serial.println(finger.status_reg, HEX);
  // Serial.print(F("Sys ID: 0x")); Serial.println(finger.system_id, HEX);
  // Serial.print(F("Capacity: ")); Serial.println(finger.capacity);
  // Serial.print(F("Security level: ")); Serial.println(finger.security_level);
  // Serial.print(F("Device address: ")); Serial.println(finger.device_addr, HEX);

  // total capacity of fingerprint
	int totalCapacity = finger.packet_len-9;
	int totalStored = finger.templateCount-2;
  Serial.print(F("Total Capasity: ")); Serial.println(totalCapacity);
  // Serial.print(F("Baud rate: ")); Serial.println(finger.baud_rate);
  finger.getTemplateCount();
  Serial.print("Sensor contains "); Serial.print(totalStored); Serial.println(" templates");
  Serial.print("Capasity Left: "); Serial.println(totalCapacity - totalStored);
  Serial.println("Waiting for valid finger...");
}

void loop()                     // run over and over again
{
  finger.getTemplateCount();
  if (finger.templateCount < 3) { /* check if sensor has stored less then two fingerprint. if it has not stored less then three fingerprint, it will be in fingerprint enroll mode, first two templet is for super admin and hidde, third one is regular admin */
    Serial.print("Sensor doesn't contain any fingerprint data. Please run the 'enroll' example.");
    getFingerprintEnroll(); /* enroll fingerprint if sensor don't have any fingerprint stored*/
  }
  // if sensor is not blank, if it has any fingerprint, it will be in wait to check fingerprint mood
  else{getFingerprintID(); delay(2000);/* check fingerprint*/}
}

// function to check fingerprint id
uint8_t getFingerprintID() {
  uint8_t p = finger.getImage();
  // duration count
  pressStartTime = millis();
  switch (p) {
    case FINGERPRINT_OK:
      Serial.println("Image taken");
      break;
    case FINGERPRINT_NOFINGER:
      Serial.println("No finger detected");
      return p;
    case FINGERPRINT_PACKETRECIEVEERR:
      Serial.println("Communication error");
      return p;
    case FINGERPRINT_IMAGEFAIL:
      Serial.println("Imaging error");
      return p;
    default:
      Serial.println("Unknown error");
      return p;
  }

  // OK success!

  p = finger.image2Tz();
  switch (p) {
    case FINGERPRINT_OK:
      Serial.println("Image converted");
      break;
    case FINGERPRINT_IMAGEMESS:
      Serial.println("Image too messy");
      return p;
    case FINGERPRINT_PACKETRECIEVEERR:
      Serial.println("Communication error");
      return p;
    case FINGERPRINT_FEATUREFAIL:
      Serial.println("Could not find fingerprint features");
      return p;
    case FINGERPRINT_INVALIDIMAGE:
      Serial.println("Could not find fingerprint features");
      return p;
    default:
      Serial.println("Unknown error");
      return p;
  }

  // OK converted!
  p = finger.fingerSearch();
  if (p == FINGERPRINT_OK) {
    Serial.println("Found a print match!");
  } else if (p == FINGERPRINT_PACKETRECIEVEERR) {
    Serial.println("Communication error");
    return p;
  } else if (p == FINGERPRINT_NOTFOUND) {
    Serial.println("Did not find a match");
    return p;
  } else {
    Serial.println("Unknown error");
    return p;
  }




  // check if admin
  if(finger.fingerID == 1 || finger.fingerID == 119){
  	if (finger.fingerID == 1) { Serial.print("\nWelcome Admin \n"); }
  	else{Serial.print("\nWelcome Super Admin \n");}
  	
    // check time till finger is on sensor
    while(p != FINGERPRINT_NOFINGER){
      p = finger.getImage();
      pressDuration = millis() - pressStartTime;
      
      // this happen between 4sec to 5sec
      if (pressDuration > 4000 && pressDuration < 5000) { Serial.print("Four second passed \n"); beep(5); }
      // this happen between 8sec to 9sec
      if (pressDuration > 8000 && pressDuration < 9000) { Serial.print("Eight second passed \n"); beeplong(500); }
      // this happen after 15 sec
      if(pressDuration > 15000 && pressDuration < 16000){ Serial.print("Fifteen second passed \n");beeplong(1000); if (finger.fingerID == 1) {break;} }
      // this happen after 25 sec
      if(pressDuration > 25000 && finger.fingerID == 119){ Serial.print("Twenty Five second passed \n");beeplong(2000); break; }
    }

    // enroll finger
    if (pressDuration > 4000 && pressDuration < 8000) {getFingerprintEnroll();delay(1000);}

    // active/deactive eprom finger
    else if (pressDuration > 8000 && pressDuration < 15000) {
      epromstatus = EEPROM.read(0);
      if(epromstatus == HIGH){ EEPROM.write(0, LOW); }
      else{ EEPROM.write(0, HIGH); Serial.print("This is ep process \n"); }
      delay(1000);
    }

    // Delete all fingerprint except the super admin
    else if(pressDuration > 15000 && pressDuration < 25000){ 
		for (int id = 1; id <= finger.templateCount-2; id++) {
			deleteFingerprint(id);
		}
    	
    	// finger.emptyDatabase(); 
    	Serial.println("All Deleted! :)"); 
    	delay(1000); 
    }
    // if the super admin press it more then 25 sec, all database including superadmin will be deleted
    else if(pressDuration > 25000 && finger.fingerID == 119){
    	finger.emptyDatabase();
    	Serial.println("All Deleted Including Super Admin! :)");
    }
  }
  // if not admin
  else{
    Serial.print("\nWelcome User! \n");
    int pinState = digitalRead(relay_pin);
    EEPROM.write(1, pinState);
    if(pinState == HIGH){ digitalWrite(relay_pin,LOW); Serial.print("Relay: Off \n"); }
    else{ digitalWrite(relay_pin,HIGH); Serial.print("Relay: On \n"); }
    beep(3);
  }  
  // found a match!
  // Serial.print("First Found ID #"); Serial.print(finger.fingerID);
  // Serial.print(" with confidence of "); Serial.println(finger.confidence);
}


// fingerprint enroll
uint8_t getFingerprintEnroll() {
  Serial.println("Ready to enroll a fingerprint!"); delay(1000);


  // // generate id
  // finger.getTemplateCount();
  // int id = finger.templateCount+1;
  // if (id == 0) { return; }
  // Serial.print("Enrolling ID #");
  // Serial.println(id);

  // generate id
  int id = 0;
  finger.getTemplateCount();
  // checks if it has stored both super admin and super user
  if (finger.templateCount < 2) { 
  	// check if it has super admin, if yes, genetares id for super user
	if (finger.templateCount < 1) {
		// generating id for super admin
		// fingerprint packet length is 128, but it can store only up to 119, so {128 - 9 = 119}
		id = finger.packet_len - 9; 
	}
	// generating id for super user
	else{ 
		// fingerprint packet length is 128, but it can store only up to 119, so {128 - 10 = 118}
		id = finger.packet_len - 10; 
	} 
  }
  // generating id for regular admin and regular user
  else{ 
  	// 2-1 = 1;
  	id = finger.templateCount-1; 
  }

  Serial.print("Enrolling ID #");
  Serial.println(id);
  if (id == 0) { return; }
  


  int p = -1;
  Serial.print("Waiting for valid finger to enroll as #"); Serial.println(id);
  while (p != FINGERPRINT_OK) {
    p = finger.getImage();
    switch (p) {
    case FINGERPRINT_OK:
      Serial.println("Image taken");
      break;
    case FINGERPRINT_NOFINGER:
      Serial.println("."); delay(1000);
      break;
    case FINGERPRINT_PACKETRECIEVEERR:
      Serial.println("Communication error");
      break;
    case FINGERPRINT_IMAGEFAIL:
      Serial.println("Imaging error");
      break;
    default:
      Serial.println("Unknown error");
      break;
    }
  }

  // OK success!

  p = finger.image2Tz(1);
  switch (p) {
    case FINGERPRINT_OK:
      Serial.println("Image converted");
      break;
    case FINGERPRINT_IMAGEMESS:
      Serial.println("Image too messy");
      return p;
    case FINGERPRINT_PACKETRECIEVEERR:
      Serial.println("Communication error");
      return p;
    case FINGERPRINT_FEATUREFAIL:
      Serial.println("Could not find fingerprint features");
      return p;
    case FINGERPRINT_INVALIDIMAGE:
      Serial.println("Could not find fingerprint features");
      return p;
    default:
      Serial.println("Unknown error");
      return p;
  }
  Serial.println("Remove finger");
  beeplong(500);
  delay(500);
  p = 0;
  while (p != FINGERPRINT_NOFINGER) {
    p = finger.getImage();
  }
  Serial.print("ID "); Serial.println(id);
  p = -1;
  Serial.println("Place same finger again");
  while (p != FINGERPRINT_OK) {
    p = finger.getImage();
    switch (p) {
    case FINGERPRINT_OK:
      Serial.println("Image taken");
      break;
    case FINGERPRINT_NOFINGER:
      Serial.print(".");delay(1000);
      break;
    case FINGERPRINT_PACKETRECIEVEERR:
      Serial.println("Communication error");
      break;
    case FINGERPRINT_IMAGEFAIL:
      Serial.println("Imaging error");
      break;
    default:
      Serial.println("Unknown error");
      break;
    }
  }

  // OK success!

  p = finger.image2Tz(2);
  switch (p) {
    case FINGERPRINT_OK:
      Serial.println("Image converted");
      break;
    case FINGERPRINT_IMAGEMESS:
      Serial.println("Image too messy");
      return p;
    case FINGERPRINT_PACKETRECIEVEERR:
      Serial.println("Communication error");
      return p;
    case FINGERPRINT_FEATUREFAIL:
      Serial.println("Could not find fingerprint features");
      return p;
    case FINGERPRINT_INVALIDIMAGE:
      Serial.println("Could not find fingerprint features");
      return p;
    default:
      Serial.println("Unknown error");
      return p;
  }

  // OK converted!
  Serial.print("Creating model for #");  Serial.println(id);

  p = finger.createModel();
  if (p == FINGERPRINT_OK) {
    Serial.println("Prints matched!");
  } else if (p == FINGERPRINT_PACKETRECIEVEERR) {
    Serial.println("Communication error");
    return p;
  } else if (p == FINGERPRINT_ENROLLMISMATCH) {
    Serial.println("Fingerprints did not match");
    return p;
  } else {
    Serial.println("Unknown error");
    return p;
  }

  Serial.print("ID "); Serial.println(id);
  p = finger.storeModel(id);
  if (p == FINGERPRINT_OK) {
    Serial.println("Stored!"); beeplong(500); delay(500);
  } else if (p == FINGERPRINT_PACKETRECIEVEERR) {
    Serial.println("Communication error");
    return p;
  } else if (p == FINGERPRINT_BADLOCATION) {
    Serial.println("Could not store in that location");
    return p;
  } else if (p == FINGERPRINT_FLASHERR) {
    Serial.println("Error writing to flash");
    return p;
  } else {
    Serial.println("Unknown error");
    Serial.print("\nError code: 0x");
    Serial.println(p, HEX);
    return p;
  }
  beep(3);
  return true;
}

void deleteFingerprint(uint8_t id) {
  uint8_t p = finger.deleteModel(id);

  if (p == FINGERPRINT_OK) {
    Serial.print("Successfully deleted ID #");
    Serial.println(id);
  } else if (p == FINGERPRINT_PACKETRECIEVEERR) {
    Serial.print("Communication error with ID #");
    Serial.println(id);
  } else if (p == FINGERPRINT_BADLOCATION) {
    Serial.print("Could not delete ID #");
    Serial.println(id);
  } else if (p == FINGERPRINT_FLASHERR) {
    Serial.print("Error writing to flash with ID #");
    Serial.println(id);
  } else {
    Serial.print("Unknown error with ID #");
    Serial.println(id);
  }
}

void beep(int value){
  int buzzcount=0;while(value!=buzzcount){
    buzzcount++;
    digitalWrite(buzz_pin,HIGH);delay(100);
    digitalWrite(buzz_pin,LOW);delay(100);
  }
}
void beeplong(int value){ digitalWrite(buzz_pin, HIGH); delay(value); digitalWrite(buzz_pin, LOW); delay(100); }

?>