#!python2
import os
import sys
import random
import string
import datetime
import codecs
import subprocess
from optparse import OptionParser
from faker import Factory
from collections import OrderedDict
from pprint import pprint
from enum import Enum
import mysql.connector
from mysql.connector import errorcode

# Use to generate random SQL INSERT statements for seed data GTOnline example project
# Project for OMS CS6400 Fall 2017
# Copyright 2017: Emile Averill 

fake = Factory.create('en_US')  #still uses foreign language for sentence/text options
full_path, script_name = os.path.split(os.path.abspath(__file__))

SQL_RESERVED = []
SQL_RESERVED.append("INTERVAL")
SQL_RESERVED.append("NOW()")
SQL_RESERVED.append("NULL")
SQL_RESERVED.append("TRUNCATE")
SQL_RESERVED.append("CURDATE")
SQL_RESERVED.append("CONCAT")

output_list = []
VERBOSE = False
DEBUG = False # True False
SHELL = False
SAVE_LOG = True

VARCHAR_SMALL = 50
VARCHAR_LARGE = 250
LOCALHOST='127.0.0.1'
GATECH_DOMAIN = 'gatech.edu'

PHP_MYADMIN_USER = 'gatechUser'
USER_PASSWORD = 'password123'

PHP_MYADMIN_PASSWORD = 'gatech123'
ADMIN_PASSWORD = 'admin123'

DB_NAME = "cs6400_fa17_team001 "
SCHEMA_SQL = 'team001_p2_schema.sql' 
OUTPUT_SQL = 'team001_p2_seed_data.sql'  
OUTPUT_SQL_LOG = OUTPUT_SQL+'.log'

def get_current_year():
    now = datetime.datetime.now()
    sys_date = str(now.strftime("%Y%m%d"))
    this_year = sys_date[:4]
    return int(this_year)

def get_current_timestamp():
    now = datetime.datetime.now()
    curr_date = now.strftime("%m/%d/%Y")
    curr_time = now.strftime("%H:%M:%S%p")
    return "%s %s"%(curr_date,curr_time)

def get_random_timestamp():
    year = random.randint(1970,get_current_year())
    month = random.randint(1,12)
    day = random.randint(1,28)
    hour = random.randint(1,12)
    min = random.randint(1,60)
    sec = random.randint(1,60)
    random_timestamp = datetime.datetime(year,month,day,hour,min,sec)

    comment_date = random_timestamp.strftime("%m/%d/%Y")
    comment_time = random_timestamp.strftime("%H:%M:%S%p")
    return "%s %s"%(comment_date,comment_time)

OUTPUT_STR = ""
ERROR_STR = "Error Log: {} \nCreated: {}".format(DB_NAME,get_current_timestamp())

def launch_shell(win_exe,cmd_str ='',option_str=''):
    win_path  = "C:\\Windows\\System32\\%s.exe" % win_exe
    winCommand = "%s %s %s" % (win_path,cmd_str,option_str)

    p=subprocess.Popen(winCommand,stdout=subprocess.PIPE,stderr=subprocess.PIPE,shell=True)               
    (output,err)=p.communicate()
    p_status=p.wait()
    if err:
        print "Error: ", err
    output_str = "%s\n%s"% (winCommand,output)
    print output_str
    return output_str

def start_mysql_cmdline():
    #add to path if needed: C:\Bitnami\wampstack-7.1.2-0\mysql\bin\mysql.exe
    sql_cmd = "mysql.exe --user={} --password={} {}".format(PHP_MYADMIN_USER,PHP_MYADMIN_PASSWORD,DB_NAME)
    # '/k' option keeps shell open, enter 'exit' to quit
    os.system("start cmd /k {}".format(sql_cmd))
        
def drop_database():
    try:
        sql = "DROP DATABASE IF EXISTS {};".format(DB_NAME)
        print sql
        connection = mysql.connector.connect(user=PHP_MYADMIN_USER, password=PHP_MYADMIN_PASSWORD,host=LOCALHOST)
        cursor = connection.cursor()
        cursor.execute(sql)
        cursor.close()
        connection.close()
    except mysql.connector.Error as err:
        print("Failed to drop database: {}".format(err))

def create_database():
    try:
        sql = "CREATE DATABASE IF NOT EXISTS {} DEFAULT CHARACTER SET utf8mb4 DEFAULT COLLATE utf8mb4_unicode_ci;".format(DB_NAME)
        print sql
        connection = mysql.connector.connect(user=PHP_MYADMIN_USER, password=PHP_MYADMIN_PASSWORD,host=LOCALHOST)
        cursor = connection.cursor()
        cursor.execute(sql)
        cursor.close()
        connection.close()
    except mysql.connector.Error as err:
        print("Failed to create database: {}".format(err))
        
def connect_to_database():
    try:
        connection = mysql.connector.connect(user=PHP_MYADMIN_USER, password=PHP_MYADMIN_PASSWORD,host=LOCALHOST,database=DB_NAME)
        cursor = connection.cursor()
        return connection
    except mysql.connector.Error as err:
        if err.errno == errorcode.ER_BAD_DB_ERROR:
            #connection = mysql.connector.connect(user=PHP_MYADMIN_USER, password=PHP_MYADMIN_PASSWORD,host=LOCALHOST)
            #cursor = connection.cursor()
            create_database()
            connect_to_database()
        else:
            print("Failed connecting to database: {}".format(err))
      
def run_sql_script(input_file):
    try:
        connection = mysql.connector.connect(user=PHP_MYADMIN_USER, password=PHP_MYADMIN_PASSWORD,host=LOCALHOST,database=DB_NAME)
        cursor = connection.cursor()
        error_str = ''

        sql_fd = open(input_file, 'r')
        sql_file = sql_fd.read()
        sql_cmds = sql_file.split(';')

        for command in sql_cmds:
            try:
                if command.strip() != '':
                    command = command +';'
                    if VERBOSE:
                        print command
                    cursor.execute(command)
            except mysql.connector.Error as err:
                error_str += "\nFailed inserting: {} {}".format(err,command)
                print error_str
                
        connection.commit()
        sql_fd.close()
        cursor.close()
        connection.close()
    
    except mysql.connector.Error as err:
        print("Failed connecting to database: {}".format(err))
    return error_str


def save_output_txt(filepath, filename, output_str):
    ouput_path = os.path.join(filepath,filename)
    with codecs.open(ouput_path,"w",encoding='utf-8') as text_file: 
        text_file.write(output_str)
    text_file.close()


def sanitize_input(input_str,title=False,big_data=False):
    sanitized = input_str
    
    #limit to VARCHAR(50) data limit (job_title,employer_name,etc.) [:50]
    if big_data:
        sanitized = sanitized[:VARCHAR_LARGE]  #[:250]
    else:
        sanitized = sanitized[:VARCHAR_SMALL]  #[:50]
    invalid_chars = "{}[],<>#+*?$@&%$!.;\\"
    for char in invalid_chars:
        if char in input_str:
            sanitized = sanitized.replace(char,":")
    sanitized = sanitized.replace("/"," / ")
    sanitized = sanitized.replace("'","")
    #get title capitalization first letter to work with '/'
    if title:
        sanitized = string.capwords(sanitized)
        sanitized = sanitized.replace(" / ","/") 
    if DEBUG:
        print "input_str: %s \t(len:%s)" % (input_str, len(input_str))
        print "sanitized: %s" % sanitized
        if len(input_str) > VARCHAR_SMALL:
            print "\n~~ERROR: '%s' TRUNCATED by %s chars\n" % (input_str, len(input_str)-VARCHAR_SMALL)
    return sanitized
    
def insertDict(table,sqlDict):
    sqlDict.keys()
    keys = ["%s" % k for k in sqlDict.keys()]
    values = ["'%s'" % v for v in sqlDict.values()]
    list_len = len(values)
    
    sql = list()
    sql.append("INSERT INTO %s (" % table)
    sql.append(",".join(keys))
    sql.append(") VALUES (")

    for idx in range(list_len):
        if any(keyword in values[idx].upper() for keyword in SQL_RESERVED):
            values[idx] = values[idx].replace("'","")
            #remove apostrophe so INSERTS do not fail
            if idx < list_len-1:
                sql.append("%s,"%values[idx])
            else:
                #don't include commma on last val
                sql.append("%s"%values[idx])               
        else:
            if idx < list_len-1:
                sql.append("%s,"%values[idx])
            else:
                #don't include commma on last val
                sql.append("%s"%values[idx])
    sql.append(");")
    return "".join(sql)

class College(Enum):
    HIGH = 'High School'
    JUNIOR = 'Community College'
    MIL = 'Military'
    TECH = 'Technical College'
    ART = 'Art Institue' 
    UNIV = 'University'

high_school_dict =  OrderedDict([("Lakeside High School",College.HIGH.value),("North Springs High School",College.HIGH.value),("Atlanta High School",College.HIGH.value)])

junior_school_dict = OrderedDict([("Georgia Military College",College.MIL.value),("Georgia Military Academy",College.MIL.value),
                           ("Atlanta Art College",College.ART.value),("Atlanta College of Art",College.ART.value),("Georgia Art College",College.ART.value),
                           ("Georgia Perimeter College",College.JUNIOR.value),("Georgia Junior College",College.JUNIOR.value),("Stanford Brown Junior College",College.JUNIOR.value),
                           ("Atlanta Technical College",College.TECH.value),("Georgia Technical College",College.TECH.value),("Atlanta Tech College",College.TECH.value)])
                           
college_dict = OrderedDict([("Massachusetts Institue of Technology",College.UNIV.value),("Carnegie Mellon University",College.UNIV.value),
                           ("California Institute of Technology",College.UNIV.value),("University of California",College.UNIV.value),
                           ("University of Colorado",College.UNIV.value),("Georgia Institute of Technology",College.UNIV.value),
                           ("University of Georgia",College.UNIV.value),("Stanford University",College.UNIV.value)])

all_schools_dict = OrderedDict(high_school_dict,**junior_school_dict)
all_schools_dict = OrderedDict(all_schools_dict,**college_dict)

def get_year_graduated(college_level,birth_year):
    avg_age = 0
    if College.HIGH.value == college_level:
        avg_age = 18
    elif College.MIL.value == college_level:
        avg_age = 21
    elif College.JUNIOR.value == college_level:
        avg_age = 21
    elif College.TECH.value == college_level:
        avg_age = 21
    elif College.ART.value == college_level:
        avg_age = 21
    elif College.UNIV.value == college_level:
        avg_age = 25
    year_graduated = int(birth_year) + int(avg_age)
    return str(year_graduated)


def build_schools():
    nlist = []
    dbTable="School"
    dbTypeTable="SchoolType"
    
    nlist.append("\n\n-- Inserting %d %ss --" % (len(College),dbTypeTable))
    for school_type in College:
        nlist.append(insertDict(dbTypeTable, OrderedDict([("type_name",school_type.value)])))

    nlist.append("\n-- Inserting %d %ss --" % (len(all_schools_dict),dbTable))
    for school_name, school_type in all_schools_dict.items():
        nlist.append(insertDict(dbTable,OrderedDict([("school_name",school_name),("school_type",school_type)])))
    output_str = "\n".join(nlist)
    if VERBOSE:
        print output_str
    return output_str
    

def build_attends(profiles,nSchools=3):
    nlist = []
    dbTable="Attend"
    
    nlist.append("\n\n-- Inserting %d %ss --" % (nSchools,dbTable))
    for userID in range(0,len(profiles)):
        
        high_school_list = random.sample(high_school_dict,1)
        for school_name in high_school_list:
            year_graduated = int(get_year_graduated(high_school_dict.get(school_name),profiles[userID].get('birthdate')[:4]))
            if year_graduated < get_current_year():
                nlist.append(insertDict(dbTable,OrderedDict([("email",profiles[userID].get('email')),("school_name",school_name),("year_graduated",str(year_graduated))])))
        if nSchools > 1:        
            junior_school_list = random.sample(junior_school_dict,1)
            for school_name in junior_school_list:
                year_graduated = int(get_year_graduated(junior_school_dict.get(school_name),profiles[userID].get('birthdate')[:4]))
                if year_graduated < get_current_year():
                    nlist.append(insertDict(dbTable,OrderedDict([("email",profiles[userID].get('email')),("school_name",school_name),("year_graduated",str(year_graduated))])))
        if nSchools > 2:         
            college_list = random.sample(college_dict,1)
            for school_name in college_list:
                year_graduated = int(get_year_graduated(college_dict.get(school_name),profiles[userID].get('birthdate')[:4]))
                if year_graduated < get_current_year():
                    nlist.append(insertDict(dbTable,OrderedDict([("email",profiles[userID].get('email')),("school_name",school_name),("year_graduated",str(year_graduated))])))
    output_str = "\n".join(nlist)
    if VERBOSE:
        print output_str
    return output_str

def build_admins(profiles):
    nlist = []
    dbTable="AdminUser"
    nAdmins = 0
    nlist.append("\n\n-- Inserting %ss --" %  (dbTable))
    for userID in range(0,len(profiles)):
        if profiles[userID].get('isAdmin') == True:
            nAdmins += 1
            nlist.append(insertDict(dbTable,OrderedDict([("email",profiles[userID].get('email')),("last_login",'NOW()')])))
    output_str = "\n".join(nlist)
    if VERBOSE:
        print output_str
    return output_str

def build_users(profiles):
    nlist = []
    dbTable="User"
    nUsers = len(profiles)
    nlist.append("\n\n-- Inserting %d %ss --" % (nUsers, dbTable))
    for userID in range(0,nUsers):
            nlist.append(insertDict(dbTable,OrderedDict([("email",profiles[userID].get('email')),("password",profiles[userID].get('password')),("first_name",profiles[userID].get('first_name')),("last_name",profiles[userID].get('last_name'))])))
    output_str = "\n".join(nlist)
    if VERBOSE:
        print output_str
    return output_str

def build_regular_users(profiles):
    nlist = []
    dbTable="RegularUser"
    nUsers = len(profiles)
    nlist.append("\n\n-- Inserting %d %ss --" % (nUsers, dbTable))
    for userID in range(0,nUsers):
            nlist.append( insertDict(dbTable,OrderedDict([("email",profiles[userID].get('email')),("gender",profiles[userID].get('gender')),("birthdate",profiles[userID].get('birthdate')),("current_city",profiles[userID].get('current_city')),("home_town",profiles[userID].get('home_town'))])))
    output_str = "\n".join(nlist)
    if VERBOSE:
        print output_str
    return output_str

def build_employers(profiles):
    nlist = []
    dbTable="Employer"
    nEmps = len(profiles)
    nlist.append("\n\n-- Inserting %d %ss --" % (nEmps, dbTable))
    for empID in range(0,nEmps):
        nlist.append(insertDict(dbTable,OrderedDict([("employer_name",profiles[empID].get('prior_employer'))])))
        nlist.append(insertDict(dbTable,OrderedDict([("employer_name",profiles[empID].get('employer'))])))           
    output_str = "\n".join(nlist)
    if VERBOSE:
        print output_str
    return output_str

def build_employment(profiles,nEmployers=2):
    nlist = []
    dbTable="Employment"
    nUsers = len(profiles)
    nlist.append("\n\n-- Inserting %d %ss --" % (nUsers, dbTable))
    for userID in range(0,nUsers):
        nlist.append( insertDict(dbTable,OrderedDict([("email",profiles[userID].get('email')),("employer_name",profiles[userID].get('employer')),("job_title",profiles[userID].get('job_title'))])))
        if nEmployers > 1: 
            nlist.append( insertDict(dbTable,OrderedDict([("email",profiles[userID].get('email')),("employer_name",profiles[userID].get('prior_employer')),("job_title",sanitize_input(fake.job(),True))])))
    output_str = "\n".join(nlist)
    if VERBOSE:
        print output_str
    return output_str


# due to FK constaint need to maintain timestamps for both comments and status updates. (fk_Comment_suemail_sudatetime_StatusUpdate_email_datetime)
status_dates = []
status_dates.append('CONCAT(CURDATE()-INTERVAL 2 MONTH," 12:00:30")')
status_dates.append('CONCAT(CURDATE()-INTERVAL 3 MONTH," 11:21:55")')
status_dates.append('CONCAT(CURDATE()-INTERVAL 4 MONTH," 16:50:22")')

def build_status_updates(profiles,nUpdates=3):
    nlist = []
    dbTable="StatusUpdate"
    nUsers = len(profiles)
    if nUpdates > 3:
        nUpdates = 3   #since only 3 status_dates
    
    nlist.append("\n\n-- Inserting %d %ss --" % (nUsers*nUpdates, dbTable))
    for userID in range(0,nUsers):
        for update in range(0,nUpdates):
            if DEBUG:
                print "i:%s mod:%s \t%s %s \tdate: %s" % (update,update%len(status_dates),profiles[userID].get('first_name')[0], profiles[userID].get('last_name'), status_dates[update%len(status_dates)])
            nlist.append(insertDict(dbTable,OrderedDict([("email",profiles[userID].get('email')),("date_time",status_dates[update]),("status_text",sanitize_input(fake.catch_phrase(),big_data=True))])))
    output_str = "\n".join(nlist)
    if VERBOSE:
        print output_str
    return output_str

def build_comments(profiles,nComments=1):
    nlist = []
    dbTable="Comment"
    other_profiles = []
    other_profiles.extend(profiles)
    
    nUsers = len(profiles)
    nlist.append("\n\n-- Inserting %d %ss --" % (nUsers*nComments, dbTable))
    for userID in range(0,nUsers):
        for comment in range(1,nComments+1):
            random_id = random.randint(0,len(other_profiles)-1)
            random_status = random.randint(0,len(status_dates)-1)
            random_day = random.randint(1,28)
            nlist.append(insertDict(dbTable,OrderedDict([("email",profiles[userID].get('email')),("date_time",'CONCAT(NOW()-INTERVAL %s DAY)'%random_day),("comment_text",sanitize_input(fake.catch_phrase(),big_data=True)),("su_email",other_profiles[random_id].get('email')),("su_date_time",status_dates[random_status])])))
            other_profiles.remove(other_profiles[random_id]) #force unique for each user
            if len(other_profiles)==0:
                other_profiles.extend(profiles)            
    output_str = "\n".join(nlist)
    if VERBOSE:
        print output_str
    return output_str

date_units = []
date_units.append('WEEK')
date_units.append('MONTH')
date_units.append('YEAR')

def build_friendships(profiles,nFriends=3):
    nlist = []
    relationships = []
    relationships.append("Parent")
    relationships.append("Coworker")
    relationships.append("Sibling")
    relationships.append("Mentor")
    relationships.append("Colleague")
    relationships.append("Peer")
    relationships.append("Significant Other")
    relationships.append("Partner")
    relationships.append("Sweetheart")
    relationships.append("Cousin")
    relationships.append("Child")
    relationships.append("Grandchild")
    relationships.append("Bestie")
    relationships.append("Facebook")
    relationships.append("LinkedIn")

    other_profiles = []
    other_profiles.extend(profiles)
    
    dbTable="Friendship"
    nUsers = len(profiles)
    nlist.append("\n\n-- Inserting %d %ss --" % (nUsers*nFriends, dbTable))
    for userID in range(0,nUsers):
        for friendID in range(1,nFriends+1):
            #unconnected friend requests
            random_id_un = random.randint(0,len(other_profiles)-1)
            random_type_un = random.randint(0,len(relationships)-1)
            #make sure don't have open request to self
            if profiles[userID].get('email') != other_profiles[random_id_un].get('email'):
                nlist.append(insertDict(dbTable,OrderedDict([("email",profiles[userID].get('email')),("friend_email",other_profiles[random_id_un].get('email')),("relationship",relationships[random_type_un]),("date_connected",'NULL')])))
                other_profiles.remove(other_profiles[random_id_un]) #force unique for each user
            if len(other_profiles)==0:
                other_profiles.extend(profiles)
                
            random_id = random.randint(0,len(other_profiles)-1)
            random_date_num = random.randint(1,28)
            random_date_unit = random.randint(0,len(date_units)-1)
            random_type = random.randint(0,len(relationships)-1)
            if profiles[userID].get('email') != other_profiles[random_id].get('email'):
                nlist.append(insertDict(dbTable,OrderedDict([("email",profiles[userID].get('email')),("friend_email",other_profiles[random_id].get('email')),("relationship",relationships[random_type]),("date_connected",'CONCAT(CURDATE()-INTERVAL %s %s)'%(random_date_num,date_units[random_date_unit]))])))
                other_profiles.remove(other_profiles[random_id]) #force unique for each user
            if len(other_profiles)==0:
                other_profiles.extend(profiles)
                
    output_str = "\n".join(nlist)
    if VERBOSE:
        print output_str
    return output_str

def build_interests(profiles,nInterests=1):
    interests = []
    interests.append("hiking")
    interests.append("camping")
    interests.append("bird watching")
    interests.append("golf")
    interests.append("skiing")
    interests.append("listening to music")
    interests.append("swimming")
    interests.append("gaming")
    interests.append("kayaking")
    interests.append("volleyball")
    interests.append("playing piano")
    interests.append("classical music")
    interests.append("base jumping")
    interests.append("photography")
    interests.append("movies")
    interests.append("reading")
    interests.append("cooking")
    interests.append("billiards")
    interests.append("theater")
    interests.append("football/soccer")
    interests.append("gardening")
    interests.append("wood working")
    interests.append("collecting")
    interests.append("riding lightrail")
    interests.append("automobiles")
    interests.append("hunting")
    interests.append("art")
    interests.append("travel")
    
    other_interests = []
    other_interests.extend(interests)
    
    nlist = []
    dbTable="UserInterest"
    nIterests = len(profiles)

    nlist.append("\n\n-- Inserting %d %ss --" % (nIterests, dbTable))
    for userID in range(0,len(profiles)):
        for interestID in range(0,nInterests):
            rand_int = random.randint(0,len(interests)-1)
            nlist.append(insertDict(dbTable,OrderedDict([("email",profiles[userID].get('email')),("interest",interests[rand_int])])))
            
            interests.remove(interests[rand_int]) #force unique for each user
            #interests = filter(lambda x: x not in interests[rand_int],interests)  #remove all instances in list
            if len(interests) == 0:
                interests.extend(other_interests)
    output_str = "\n".join(nlist)
    if VERBOSE:
        print output_str
    return output_str
 
def get_birthdate_from_age(years_old):
    rand_seed = random.randint(0,10000)
    random.seed(rand_seed)
    birthmonth = random.randint(1,12)
    birthday = random.randint(1,28)  #round down to Febuary
    now = datetime.datetime.now()
    currentYear = int(datetime.datetime.now().year)
    birth_year = str(currentYear-int(years_old))
    birth_date = birth_year+'-'+ "%02d"%birthmonth  +'-'+ "%02d"%birthday
    return birth_date

def get_email_from_name(full_name):
    full_name = full_name.lower()
    name_list = list(full_name.split())
    if len(name_list) < 3:
        email_addr = name_list[0][0]+ name_list[-1]  #if prefix exists (Dr. Miss, etc.)
    else:  
        email_addr = name_list[1][0]+ name_list[-1]
    return email_addr+'@'+GATECH_DOMAIN


def build_all_profiles(nMales=0,nFemales=0,nAdmins=0):
    profiles = []
    print "\n-- Creating %d Profiles --" % (nMales+nFemales+nAdmins+1)

    #Michael Bluth is default login for gtonline php code so maintain autologin functionality
    profiles.append(build_profile('Male',isAdmin=False,isBluth=True))

    for userID in range(0,nMales):
        profiles.append(build_profile('Male'))
    for userID in range(0,nFemales):
        profiles.append(build_profile('Female'))
    for adminID in range(1,nAdmins+1):
        if adminID%2 ==0:
            gender = 'Male'
        else:
            gender = 'Female'
        profiles.append(build_profile(gender,adminID,isAdmin=True))
    return profiles
    
def build_profile(gender=None,adminID=0,isAdmin=False,isBluth=False):
    #https://faker.readthedocs.io/en/master/locales/en_US.html
    #pprint(vars(fake))

    age = int(random.randint(25,70))
    birth_date = get_birthdate_from_age(age)

    if isBluth:
        profile = OrderedDict([("username",'michael123'),("email",'michael@bluthco.com'),("password",'michael123'),("first_name",'Michael'),("last_name",'Bluth'),("birthdate",birth_date),("gender",'Male'),
                                     ("employer",sanitize_input(fake.company())),("prior_employer",sanitize_input(fake.company())),("job_title",sanitize_input(fake.job(),True)),("current_city",fake.city()),("home_town",fake.city()),("isAdmin",False)])
    elif gender == 'Male':
        name_male = fake.first_name_male() +' ' + fake.last_name_male()
        name_list = list(name_male.split())
        email_addr = get_email_from_name(name_male)
        username = email_addr.split("@")[0]
        if isAdmin == True:
            profile = OrderedDict([("username",username),("email","admin0%s@gtonline.com"%adminID),("password",ADMIN_PASSWORD),("first_name",name_list[0]),("last_name",name_list[1]),("birthdate",birth_date),("gender",'Male'),
                                   ("employer",sanitize_input(fake.company())),("prior_employer",sanitize_input(fake.company())),("job_title",sanitize_input(fake.job(),True)),("current_city",fake.city()),("home_town",fake.city()),("isAdmin",isAdmin)])
        else:
            profile = OrderedDict([("username",username),("email",email_addr),("password",USER_PASSWORD),("first_name",name_list[0]),("last_name",name_list[1]),("birthdate",birth_date),("gender",'Male'),
                                   ("employer",sanitize_input(fake.company())),("prior_employer",sanitize_input(fake.company())),("job_title",sanitize_input(fake.job(),True)),("current_city",fake.city()),("home_town",fake.city()),("isAdmin",isAdmin)])
    else:
        name_female = fake.first_name_female() +' ' + fake.last_name_female()
        name_list = list(name_female.split())
        email_addr = get_email_from_name(name_female)
        username = email_addr.split("@")[0]
        if isAdmin == True:
            profile = OrderedDict([("username",username),("email","admin0%s@gtonline.com"%adminID),("password",ADMIN_PASSWORD),("first_name",name_list[0]),("last_name",name_list[1]),("birthdate",birth_date),("gender",'Female'),
                                   ("employer",sanitize_input(fake.company())),("prior_employer",sanitize_input(fake.company())),("job_title",sanitize_input(fake.job(),True)),("current_city",fake.city()),("home_town",fake.city()),("isAdmin",isAdmin)])
        else:
            profile = OrderedDict([("username",username),("email",email_addr),("password",USER_PASSWORD),("first_name",name_list[0]),("last_name",name_list[1]),("birthdate",birth_date),("gender",'Female'),
                                   ("employer",sanitize_input(fake.company())),("prior_employer",sanitize_input(fake.company())),("job_title",sanitize_input(fake.job(),True)),("current_city",fake.city()),("home_town",fake.city()),("isAdmin",isAdmin)])

    print "%s %s [%s] Birthdate:%s (%syo %s) %s" % (profile.get('first_name'),profile.get('last_name'),profile.get('job_title'),profile.get('birthdate'),age,profile.get('gender'),profile.get('email'))
    return profile

    '''
    print "Job: %s" % fake.job()
    print "Male: %s" % fake.name_male()
    print fake.simple_profile()
    print "City: %s" % fake.city()
    print "Address: %s" % fake.address()
    print "Phone: %s" % fake.phone_number()
    print "Domain: %s" % fake.domain_name()
    print "Credit Card: %s" % fake.credit_card_number()
    print "Company: %s" % fake.company()
    print "Password: %s" % fake.password(length=10, special_chars=True, digits=True, upper_case=True, lower_case=True)
    print "past_date: %s" % fake.past_date()
    print "Catch Phrase: %s" % fake.catch_phrase()
    print "Text: %s" % fake.text()  (NOT in English)
    print "sentence: %s" % fake.sentence()  (NOT in English)
    '''

if __name__ == "__main__":
    parser = OptionParser()
    parser.add_option("-o", "--output",
                      dest="output_filename",
                      default=OUTPUT_SQL,
                      help="output SQL file with INSERT seed data",)
    parser.add_option('-u', '--users',
                  dest="users",
                  default=8,
                  action="store",
                  type="int",
                  help="number of users",)
    parser.add_option('-s', '--schools',
                  dest="schools",
                  default=3,
                  action="store",
                  type="int",
                  help="number of schools atttended",)
    parser.add_option('-e', '--employers',
                  dest="employers",
                  default=2,
                  action="store",
                  type="int",
                  help="number of employers",)
    parser.add_option('-i', '--interests',
                  dest="interests",
                  default=4,
                  action="store",
                  type="int",
                  help="number of user interests",)
    parser.add_option('-f', '--friends',
                  dest="friends",
                  default=3,
                  action="store",
                  type="int",
                  help="number of friends",)
    parser.add_option('-t', '--texts',
                  dest="texts",
                  default=2,
                  action="store",
                  type="int",
                  help="number of status text comments",)
    parser.add_option('-v', '--verbose',
                  dest="verbose",
                  default=False,
                  action="store_true",
                  help="print output to screen",)
    parser.add_option('-d', '--debug',
                  dest="debug",
                  default=False,
                  action="store_true",
                  help="print debug output to screen",)
    parser.add_option('-c', '--console',
                  dest="console",
                  default=False,
                  action="store_true",
                  help="start mysql command line shell",)
    (options, args) = parser.parse_args()
    if options.verbose:
        VERBOSE = True
        SAVE_LOG = True
    if options.debug:
        DEBUG = True
        SAVE_LOG = True
    if options.console:
        SHELL = True
        
    drop_database()
    create_database()
    
    if os.path.exists(OUTPUT_SQL_LOG):
        os.remove(OUTPUT_SQL_LOG)
        
    ERROR_STR += run_sql_script(SCHEMA_SQL)
    
    profiles = []
    profiles = build_all_profiles(nMales=options.users,nFemales=options.users,nAdmins=options.users/2)
    OUTPUT_STR += "\nUSE %s;" % DB_NAME

    #fk_RegularUser_email_User_email (ORDER MATTERS: insert users prior to regular_users)
    OUTPUT_STR += build_users(profiles)
    OUTPUT_STR += build_regular_users(profiles)
    OUTPUT_STR += build_admins(profiles) 
    
    OUTPUT_STR += build_schools()
    OUTPUT_STR += build_attends(profiles,nSchools=options.schools) #1 high, 2 jr, 3 univ
    
    OUTPUT_STR += build_employers(profiles)
    OUTPUT_STR += build_employment(profiles,nEmployers=options.employers)
    
    OUTPUT_STR += build_interests(profiles,nInterests=options.interests)
    OUTPUT_STR += build_status_updates(profiles)
    OUTPUT_STR += build_comments(profiles,nComments=options.texts)

    OUTPUT_STR += build_friendships(profiles,nFriends=options.friends)

    if options.output_filename:
        save_output_txt(full_path,options.output_filename,OUTPUT_STR)
    else:    
        save_output_txt(full_path,OUTPUT_SQL,OUTPUT_STR)
        
    ERROR_STR += run_sql_script(options.output_filename)
    
    if SAVE_LOG:
        save_output_txt(full_path,OUTPUT_SQL_LOG,ERROR_STR)
            
    if SHELL:
        start_mysql_cmdline()
    
    print "\n%s complete..." % (script_name.upper())    
