# blipblop
Egy videomegosztó oldal projektje.

Az elkészült weboldalon létezik egy admin-fiók:

- Felhasználónév: blipblopadmin
- Jelszó: admin000

(az admin további adminokat is képes beállítani felhasználókból)

## FONTOS
Ahhoz, hogy használni tudd, létre kell hozni egy fájlt, amit '.env' néven kell elnevezni. Ebbe kell belerakni az adatbázis felhasználóneved és jelszavad.

## Lásd a sablont .env.example fájlban!

## További tudnivalók:

1. Az adatbázis az alábbi docker serveren fut:

- https://hub.docker.com/r/truevoly/oracle-12c

 
2. A szerverek futásához a Tanár Úr videóit vettük segítségül: 

- https://www.inf.u-szeged.hu/~gnemeth/kurzusok/adatbalapugyakorlat/Oracle_bevezeto/az_oracle_adatbzis_szerver_teleptse_docker_kpmsbl.html

- https://www.inf.u-szeged.hu/~gnemeth/kurzusok/adatbalapugyakorlat/Oracle_bevezeto/csatlakozs_az_oracle_12chez.html

3. Az sqldeveloperben először csatlakozni kell a szerverhez, aztán azon belül hoztunk létre egy új felhasználót, akivel aztán az applikációnkban is csatlakozunk az adatbázishoz. Fontos volt hogy ennek a felhasználónak minden jogot meg kellett adni a létrehozott táblákhoz, hogy az applikációban megfelelően működjenek a lekérdezések.

4. Ezen a létrehozott sqldeveloper fiókon futtatjuk a megadott script.sql-t, ami létrehozza a táblákat és a példa rekordokat.

5. A weboldalt a PhpStorm-ból indítottuk, azon belül is az index.php fájlt kell vagy a Built-in Preview-val, vagy (leginkább) Google Chrome böngészőben.

- Segítségkéréshez kérem írjon emailt: spitz.marko@gmail.com