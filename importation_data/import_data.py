##Importation de fichier csv en base de données
##Paul Giroux
##create : 30/12/2021
##Update : 01/01/2022

import pymysql.cursors
import csv

def is_integer(n):
    try:
        float(n)
    except ValueError:
        return False
    else:
        return float(n).is_integer()

def reduction_date(d):
    mois = [None, 'janvier', 'fevrier', 'mars', 'avril', 'mai', 'juin', 'juillet', 'aout', 'septembre', 'octobre', 'novembre', 'decembre']
    if(d[0] == 'l'):
        d = d[6:]
    elif d[0] == 'm' and d[1] == 'a':
        d = d[6:]
    elif d[0] == 'm' :
        d = d[8:]
    elif d[0] == 'j' :
        d = d[6:]
    elif d[0] == 'v':
        d = d[8:]
    if is_integer(d[1]) : 
        n = int(d[0] + d[1])
        d = d[3:]
    else:
        n = int(d[0])
        d = d[2:]
    mois_select = d[:-7]
    m = mois.index(mois_select)
    if m<10 : m = "0"+str(m)
    if n<10 : n = "0"+str(n)
    d = d[(len(mois_select)+2):]+":00"
    d = "2022-"+m+"-"+n+" "+d
    return(d)


# Connectez- vous à la base de données.
connection = pymysql.connect(host='127.0.0.1',
                             user='root',
                             password='',                             
                             db='edt',
                             charset='utf8mb4',
                             cursorclass=pymysql.cursors.DictCursor) 
print ("connect successful!!") 
elleves = []
elleves_id = []
profs = []
profs_id = []
add = []

try:
    with connection.cursor() as cursor: 
        # SQL 
        sql = "SELECT * FROM proph " 
        # Exécutez la requête (Execute Query).
        cursor.execute(sql) 
        for row in cursor:
            profs += [[row['nom'], row['prenom']]]
            profs_id += [[row['nom'], row['prenom'], row['id']]]
    with connection.cursor() as cursor: 
        # SQL 
        sql = "SELECT * FROM elleve " 
        # Exécutez la requête (Execute Query).
        cursor.execute(sql) 
        for row in cursor:
            elleves += [[row['nom'], row['prenom']]]
            elleves_id += [[row['nom'], row['prenom'], row['id']]]

    with open("add.csv", newline='', encoding='utf-8') as f:
        reader = csv.reader(f)
        for row in reader:
            add += [row]

    for a in add:
        ell = [a[2], a[3]]
        pr = [a[5], a[6]]

        if ell in elleves :
            print('',end="")
        elif ell['nom'][0] != 0 :
            try :
                with connection.cursor() as cursor: 
                    # SQL 
                    sql = "insert into `elleve` (`nom`, `prenom`, `classe`) values (%s,%s,%s)" 
                    # Exécutez la requête (Execute Query).
                    cursor.execute(sql, (a[2], a[3], a[4])) 
                connection.commit()
                elleves = []
                elleves_id = []
                print("elleve ", a[2], " ", a[3] , " ajouter ok")
            except :
                print("erreur d'ajout avec l'elleve : " , a[2], " ", a[3])
            with connection.cursor() as cursor: 
                # SQL 
                sql = "SELECT * FROM elleve " 
                # Exécutez la requête (Execute Query).
                cursor.execute(sql) 
                print ("cursor.description: ", cursor.description) 
                print()
                for row in cursor:
                    elleves += [[row['nom'], row['prenom']]]
                    elleves_id += [[row['nom'], row['prenom'], row['id']]]

        if pr in profs :
            print('',end="")
        elif pr['nom'][0] != 'n':
            try :
                with connection.cursor() as cursor: 
                    # SQL 
                    sql = "insert into `proph` (`nom`, `prenom`) values (%s,%s)" 
                    # Exécutez la requête (Execute Query).
                    cursor.execute(sql, (a[5], a[6])) 
                connection.commit()
                profs = []
                profs_id = []
                print("prof ", a[5], " ", a[6] , " ajouter ok")
            except :
                print("erreur d'ajout avec le prof : " , a[2], " ", a[3])
            with connection.cursor() as cursor: 
                # SQL 
                sql = "SELECT * FROM proph " 
                # Exécutez la requête (Execute Query).
                cursor.execute(sql) 
                print ("cursor.description: ", cursor.description) 
                print()
                for row in cursor:
                    profs += [[row['nom'], row['prenom']]]
                    profs_id += [[row['nom'], row['prenom'], row['id']]]
        
        #creation du tableau d'ajout
        i = add.index(a)
        date = ""
        if(a[0][0] != 'd'):
            try :
                date = a[0]
                date_red = reduction_date(date)
                id_pr = int(profs_id[profs.index(pr)][2])
                id_el = int(elleves_id[elleves.index(ell)][2])
                
                lieu = a[8]
                durre = int(a[1])
                detail = a[7]
                couleur = a[9]
            except :
                print("erreur en ligne : ", i+1)
            
            try :
                with connection.cursor() as cursor: 
                    # SQL 
                    sql = "insert into `rdv` (nom,date,durre,couleur,id_elleve,id_proph,lieu) values (%s,%s,%s,%s,%s,%s,%s)" 
                    # Exécutez la requête (Execute Query).
                    cursor.execute(sql, (detail, date_red, int(durre), couleur, int(id_el), int(id_pr), lieu)) 
                connection.commit()
            except :
                print("erreur d'ajout avec le rdv ligne : " , i)

finally:
    # Closez la connexion (Close connection).      
    connection.close()
    input()
