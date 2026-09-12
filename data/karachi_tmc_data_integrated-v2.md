# Karachi Town Municipal Corporations (TMCs) — Structured Contact Dataset

**Purpose:** Consolidated TMC information supplied for the Karachi civic-routing AI.

**Data status:** User-provided working dataset. Contacts, office locations, names, and jurisdiction descriptions should be independently verified before citizen-facing or automated use.

**Last updated:** 12 September 2026

## Important implementation rules

- Treat `N/A`, `-`, and blank fields as null values, not as contact information.
- Preserve phone numbers exactly as supplied until a human verifies and normalizes them.
- Do not route solely from a locality name where a map pin is available; use the geospatial boundary or confirm with the town office.
- Some staff and UC contacts are personal mobile numbers. Do not expose or message them automatically without consent and verification.
- Use the town office's main website/email/phone first. Staff and UC contacts should be fallback/internal escalation data.
- The supplied material contains possible inconsistencies, including repeated staff lists and overlapping area descriptions. Keep the raw record and add a verification flag rather than silently correcting it.

## Data schema

Each town record contains:

- `district`
- `town_name`
- `areas`
- `website`
- `email`
- `phone`
- `address`
- `map_location`
- `chairman`
- `municipal_commissioner`
- `staff_contacts`
- `union_councils`

## Authority-routing reminder

This file contains TMC contacts only. It does not replace sector authorities:

- Water supply, sewerage, sewer manholes, and burst water mains may require KWSC.
- Municipal solid waste may require SSWMB.
- KMC roads, major drains/nullahs, major roads, bridges, and KMC assets may require KMC.
- Electricity and many street-light faults may require K-Electric.
- Cantonment areas may require the relevant Cantonment Board.

## TMC directory

                      ## 1. District Karachi South


### TMC Saddar
Areas: Saddar, Civil Lines, Clifton (parts), DHA (some phases), I.I. Chundrigar Road, etc.
website: https://tmcsaddar.gos.pk/
Phone: N/A
Email: N/A
map location:https://www.google.com/maps/dir//Town+Municipal+Corporation+Saddar+TMC-Saddar+near+ocean+mall,+V225%2BMQ4,+Haqqani+Chowk,+New+Chali,+Karachi,+Pakistan/@24.8676352,67.059712,14z/data=!3m1!4b1!4m8!4m7!1m0!1m5!1m1!1s0x3eb33f02e4da7483:0x703a7a02aeafe9ee!2m2!1d67.0094644!2d24.8516271?entry=ttu&g_ep=EgoyMDI2MDkwOS4wIKXMDSoASAFQAw%3D%3D
Address: V225+MQ4, Haqqani Chowk, New Chali, Karachi


Chairman: Mansoor Ahmed Shaikh

Municipal Commissioner: Noor Hassan Jokhio

Staff List

Designation	Name	Contact
HRM	MUHAMMAD OWAIS ABBASI	0300-9256805
LAND	MUHAMMAD NAEEM KHAn	0343-3148831
ADVERTISEMENT	FAHEEM RAZA SHAIKH	0334-3352777
ACCOUNTS	MIMRAN SIYAL	0300-3296080
INFORMATION	RASHID ANSARI	N/A
EDUCATION	MUSHTAQ SOMROO	0300-9786364
ANTI ENCROACHMENT	FAHAD MUSTAFA	0316-2666363
AUDIT	MUHAMMAD AMAN QURASHI	0300-2633744
SENIOR ACCOUNTS	ABDUL NAEEM	N/A
EDUCATION	AYOUB JUZBANIL	N/A
ASSISTANT EXECUTIVE ENGINEER B & R	SHAIKH MUHAMMAD ALAM	0322-2041247
DEPUTY DIRECTOR M & E	ADNAN AHMED	0311-2503245
DEPUTY DIRECTOR M & E	REHAN JABBAR	N/A
DIRECTOR SANITATION	AFTAB ALAM	0317-8394455
DIRECTOR PURCHASE	ZAHID IQBAL	0334-2701549


Union Councils List

S.No	UC	Name of UC	Chairman / Vice Chairman	Contact No
1	UC-01	Fareed Colonys	Muhammad Kamran (Vice Chairman)	0300-2544710
2	UC-02	Haryana Colony	Abdullah Baloach (Town Vice Chairman)	0312-215 0345
3	UC-03	Bismillah Colony	Asif Rehman (Vice Chairman)	0345-1992508
4	UC-04	Islam Nagar	Abid Shah (Vice Chairman)	0344-2606660
5	UC-05	Mominabad	Malik Arif Awan (Town Chairman)	0300-9276565
6	UC-06	Frontier Colony	Mukhtiar Shah (Vice Chairman)	0331-2107024
7	UC-07	Banaras Colony	Dr. Kabeer (Vice Chairman)	0333-2280585
8	UC-08	Peerabad	Nadir ur Rehman (Vice Chairman)	0311-2977133
9	UC-09	Qasba Colony	Syed Baseer Uddin (Vice Chairman)	0344-5532288




**TMC Jamshed**
Areas: Jamshed Town, PECHS, Jamshed Quarters, etc.
Website: https://tmcjinnah.gos.pk/
Email: tmcjinnah3@gmail.com
Phone: 0316 3779792
Address: V354+4V6, Sindhi Muslim Cooperative Housing Society Block B Sindhi Muslim CHS (SMCHS), Karachi
Map Location: https://www.google.com/maps/dir//Chanesar+Town+Office+-+TMC+Chanesar,+V354%2B4V6,+Sindhi+Muslim+Cooperative+Housing+Society+Block+B+Sindhi+Muslim+CHS+(SMCHS),+Karachi,+Pakistan/@24.8676352,67.059712,14z/data=!4m8!4m7!1m0!1m5!1m1!1s0x3eb33e833d864267:0xf85c1a2c961f9895!2m2!1d67.0571445!2d24.8577828?entry=ttu&g_ep=EgoyMDI2MDkwOS4wIKXMDSoASAFQAw%3D%3D



Staff List
Designation	Name	Contact
HRM	MUHAMMAD OWAIS ABBASI	0300-9256805
LAND	MUHAMMAD NAEEM KHAn	0343-3148831
ADVERTISEMENT	FAHEEM RAZA SHAIKH	0334-3352777
ACCOUNTS	MIMRAN SIYAL	0300-3296080
INFORMATION	RASHID ANSARI	N/A
EDUCATION	MUSHTAQ SOMROO	0300-9786364
ANTI ENCROACHMENT	FAHAD MUSTAFA	0316-2666363
AUDIT	MUHAMMAD AMAN QURASHI	0300-2633744
SENIOR ACCOUNTS	ABDUL NAEEM	N/A
EDUCATION	AYOUB JUZBANIL	N/A
ASSISTANT EXECUTIVE ENGINEER B & R	SHAIKH MUHAMMAD ALAM	0322-2041247
DEPUTY DIRECTOR M & E	ADNAN AHMED	0311-2503245
DEPUTY DIRECTOR M & E	REHAN JABBAR	N/A
DIRECTOR SANITATION	AFTAB ALAM	0317-8394455
DIRECTOR PURCHASE	ZAHID IQBAL	0334-2701549


Union Councils List
S.No	UC	Name of UC	Chairman / Vice Chairman	Contact No
1	UC-01	Fareed Colonys	Muhammad Kamran (Vice Chairman)	0300-2544710
2	UC-02	Haryana Colony	Abdullah Baloach (Town Vice Chairman)	0312-215 0345
3	UC-03	Bismillah Colony	Asif Rehman (Vice Chairman)	0345-1992508
4	UC-04	Islam Nagar	Abid Shah (Vice Chairman)	0344-2606660
5	UC-05	Mominabad	Malik Arif Awan (Town Chairman)	0300-9276565
6	UC-06	Frontier Colony	Mukhtiar Shah (Vice Chairman)	0331-2107024
7	UC-07	Banaras Colony	Dr. Kabeer (Vice Chairman)	0333-2280585
8	UC-08	Peerabad	Nadir ur Rehman (Vice Chairman)	0311-2977133
9	UC-09	Qasba Colony	Syed Baseer Uddin (Vice Chairman)	0344-5532288



### TMC Liaquatabad
Areas: Liaquatabad Town, Shahrah-e-Ibne Sina, Gujar Nala, Nazimabad No.2, Karachi, Sindh.
Website: https://tmcliaquatabad.gos.pk/
Email: tmcliaqutabad@gmail.com
Phone: (021) 99260388
Address: Liaquatabad Town, Shahrah-e-Ibne Sina, Gujar Nala, Nazimabad No.2, Karachi, Sindh.
Map location: https://www.google.com/maps/dir//W25Q%2BQ79+Liaquatabad+Town+Office,+Sir+Shah+Muhammad+Suleman+Rd,+Nazimabad+No.+4+Block+4+Nazimabad,+Karachi,+74600,+Pakistan/@24.9094045,67.0021837,14z/data=!4m17!1m8!3m7!1s0x3eb33fe663ba1785:0xd5a5b5065939e11c!2sLiaquatabad+Town+Office!8m2!3d24.9094045!4d67.0382326!15sCkxMaWFxdWF0YWJhZCBUb3duIFNoYWhyYWggZSBJYm5lIFNpbmEgR3VqYXIgTmFsYSBOYXppbWFiYWQgTm8gMiBLYXJhY2hpIFNpbmRokgEQY29ycG9yYXRlX29mZmljZeABAA!16s%2Fg%2F11h6nthhbt!4m7!1m0!1m5!1m1!1s0x3eb33fe663ba1785:0xd5a5b5065939e11c!2m2!1d67.0382326!2d24.9094045?entry=ttu&g_ep=EgoyMDI2MDkwOS4wIKXMDSoASAFQAw%3D%3D


Staff List
Designation	Name	Contact
Medical	Asif Zaidi	03111034647
Anti Encroachment	Nasir	03002147861
Director M&E (Mechanical)	Syed Zia Haider	03002212702
Director M&E (Electrical)	Asghar	03003737637
Director Council	Waseem	03062165166
Director Information	Faheem Zaidi	03002290488
Director Library	Liaquat	03110909779
Director B&R	Hassan Abbas	03333446992
Director Parks	Rehan Hamdani	03002416540
Director Education	Fareed Raza	03333506078
Director Sanitation	Anwar Jamal	03344228000
Municipal Commisioner	Darya Khan Pitafi	03453878468
Director Administration	Faiz Ahmed	03002215923



Union Councils List
S.No	UC	Name of UC	Chairman / Vice Chairman	Contact No
1	UC-03	Bandhani Colony	Younus Bandhani	03212553041
2	UC-02	Sharifabad	Hafiz Irshad	03118476731
3	UC-01	Moosa Colony	Ubaid Saharanpuri	03442266458
4	UC-04	Ibn e Sina	Ubaid Ahmed Khan	03343739408
5	UC-05	Commercial Area	Qutubuddin	03343638772
6	UC-06	B-1 Area	Syed Moin Abbas Madani	03332288032
7	UC-07	C-Area	Azhar Shamsi	03222527506



### TMC Baldia
Areas: Baldia Town, Saeedabad, parts of Orangi vicinity.
Website: https://tmcbaldia.gos.pk/
Email: Tmcbaldia@gmail.com
Phone: (021) 99334226
Address: TOWN MUNICIPAL CORPORATION, Hub River Road Baldia Town Karachi, Sindh
Map Location: https://www.google.com/maps/dir//TOWN+MUNICIPLE+CORPORATION+(BALDIA)+DISTRICT-KEAMARI,+WX73%2B8CP,+Sector+5+Baldia,+Karachi,+Pakistan/@24.8676352,67.059712,14z/data=!3m1!4b1!4m8!4m7!1m0!1m5!1m1!1s0x3eb31527af20b9c7:0x3ff9270fa6025c7!2m2!1d66.9537054!2d24.9134345?entry=ttu&g_ep=EgoyMDI2MDkwOS4wIKXMDSoASAFQAw%3D%3D


Union Councils List
S.No	UC	Name of UC	Chairman / Vice Chairman	Contact No
1	UC-01	Methan	Asif Raza (Chairman)	0348-2108006
2	UC-02	Ittehad Town	Muhammad Arif (Chairman)	0333-2298299
3	UC-03	Shaheed Nawab Khan	Jamroz Khan (Chairman)	0300-2710046
4	UC-04	Gulshan-e-Ghazi	Murad Khan (Chairman)	0315-8285077
5	UC-05	Islam Nagar	Dildar Ahmed (Chairman)	0345-6227787
6	UC-06	Jam Nagar	Muhammad Ahad Iqbal (Chairman)	0333-2272903
7	UC-07	Madina Colony	Ellahi Bux (Chairman)	0300-3642984
8	UC-08	Saeedabad	Muhammad Farooq (Chairman)	0312-9227225
9	UC-09	Nai Abadi Ranger Muhalla	Zeeshan Zaheer (Chairman)	0347-2056270
10	UC-10	Shaheed Eidi Ameen	Muhammad Akram (Chairman)	-
11	UC-11	New Saeedabad	Khan Muhammad	0341-2557447
12	UC-12	Naval Colony	Dildar (Chairman)	0300-2617403
13	UC-13	Yousuf Goth	Chairman	-


Town Chairman Baldia, District Keamari: Abdul Karim Askani



         ## 2. District Karachi East

### TMC Gulshan-e-Iqbal
Areas: Gulshan-e-Iqbal, parts of Gulistan-e-Johar, NIPA, etc.
Website: https://tmcgulshan.gos.pk/
Email: tmcgulshan@gmail.com
Phone: N/A
Address: TMC Gulshan-e-Iqbal near Civic center University Road Block14 Gulshan-e-Iqbal Karachi, Sindh.
Map Location: https://www.google.com/maps/dir//V3XC%2B83G+TMC+Gulshan+office,+Block+14+Gulshan-e-Iqbal,+Karachi/data=!4m6!4m5!1m1!4e2!1m2!1m1!1s0x3eb33f007bcab1a3:0x5f449715dc51f1e?sa=X&ved=1t:57443&ictx=111


Staff List
Designation	Name	Contact
ADMINISTRATION	RAEES AHMED	0333-3974202
INFOMATION TECNOLOGY (I.T)	AZEEM HAIDER	0333-1260033
INFOMATION	SYED MUJTUBA	0332-3090447
ACCOUNTS	NAVEED KOLACHI	0300-2105887
INTERNAL AUDIT	IMRAN KAZMI	N/A
ADVERTISEMENT	SHERYAR KHARAL	N/A
EDUCATION	AZEEM HAIDER	0333-1260033
LIBRARY	TALAT SHAKEEL	0333-3209313
PARKS	GULAM RASOOL	0321-9224790
MUNICIPAL SERVICES	RAO SHAMSHAD	0304-2514891
LAND & ANTI ENCROACHMENT	MIRZA WAQAS BAIG	0345-3198606
EXECUTIVE ENGINEER B & R	RASHID FAYYAZ	0345-2071998
MECHANICAL AND ENGINEERING	JAVED AHMED BHAYO	0300-2701988
DIRECTOR PURCHASE	RAEES AHMED (ADD)	0333-3974202


Union Councils List
S.No	UC	Name of UC	Chairman / Vice Chairman	Contact No
1	UC-01	Essa Nagri.	Sanam Gabol (Chairman)	0312-2441923
2	UC-02	Hassan Square.	Riaz Azhar (Chairman)	0302-8274361
3	UC-03	Zia Ul Haq Colony.	Majid Ali (Chairman)	0300-3332203
4	UC-04	Disco Bakery.	Fayyaz Ul Huda (Chairman)	0311-1647429
5	UC-05	Quaid-E-Azam Colony.	Muzafar Iqbal (Chairman)	0334-3653545
6	UC-06	Metroville.	Nasir Ashfaq (Chairman)	0300-8258780
7	UC-07	Shanti Nagar.	Sikandar Baloch (Chairman)	0300-2264407
8	UC-08	National Stadium.	Khizar Baqi (Chairman)	0333-2223962


Chairman: Dr.FauadAhmed



### TMC Gulberg
Areas: Gulberg Town, parts of North Nazimabad vicinity.
Website: https://tmcgulberg.gos.pk/
Email: N/A
Phone: 0324 2920766
Address: 1185 Rashid Minhas Rd, Federal B Area Block 16 Gulberg Town, Karachi, Karachi City, Sindh 75950
Map Location: http://google.com/maps/dir//TMC+Cricket+Ground+Gulberg+Back+gate,+Block+13+Gulberg+Town,+Karachi,+Pakistan/@24.8676352,67.059712,14z/data=!3m1!4b1!4m8!4m7!1m0!1m5!1m1!1s0x3eb33f82b4e59355:0x827df6c45b037d3d!2m2!1d67.0703021!2d24.939081?entry=ttu&g_ep=EgoyMDI2MDkwOS4wIKXMDSoASAFQAw%3D%3D


Staff List
Designation	Name	Contact
HRM	MUHAMMAD OWAIS ABBASI	0300-9256805
LAND	MUHAMMAD NAEEM KHAn	0343-3148831
ADVERTISEMENT	FAHEEM RAZA SHAIKH	0334-3352777
ACCOUNTS	MIMRAN SIYAL	0300-3296080
INFORMATION	RASHID ANSARI	-
EDUCATION	MUSHTAQ SOMROO	0300-9786364
ANTI ENCROACHMENT	FAHAD MUSTAFA	0316-2666363
AUDIT	MUHAMMAD AMAN QURASHI	0300-2633744
SENIOR ACCOUNTS	ABDUL NAEEM	-
EDUCATION	AYOUB JUZBANIL	-
ASSISTANT EXECUTIVE ENGINEER B & R	SHAIKH MUHAMMAD ALAM	0322-2041247
DEPUTY DIRECTOR M & E	ADNAN AHMED	0311-2503245
DEPUTY DIRECTOR M & E	REHAN JABBAR	-
DIRECTOR SANITATION	AFTAB ALAM	0317-8394455
DIRECTOR PURCHASE	ZAHID IQBAL	0334-2701549



Union Councils List
S.No	UC	Name of UC	Chairman / Vice Chairman	Contact No
1	UC-08	Ayesha Manzil	Shallal Ahmed ( Vice Chairman)	0312-215 0345
2	UC-07	Hussainabad	Ilyas Memon (Vice Chairman)	0345-1992508
3	UC-06	Azizabad	Muhammad Ilyas (Vice Chairman)	0344-2606660
4	UC-05	Yaseenabad	Arif Munir(Chairman)	0300-9276565
5	UC-04	Naseerabad	Owais Baig(Vice Chairman)	0331-2107024
6	UC-03	Waterpump	Noman ul haq(Vice Chairman)	0333-2280585
7	UC-02	Samanabad	Faisal Sheikh (Chairman)	0311-2977133
8	UC-01	Shafiq Mill	Asim Makhdomi (Chairman)	0344-5532288



Chairman: Maalik Arif



### TMC Jinnah
Areas: Jinnah Garden, parts of central Karachi.
Website: https://tmcjinnah.gos.pk/
Email: tmcjinnah3@gmail.com
Phone: 0316 3779792
Address: TMC Jinnah Office, Block 14 Gulshan-e-Iqbal, Karachi, Near Civic Center, Karachi City, Sindh
Map Location: https://www.google.com/maps?rlz=1C1CHBF_en-GBPK1179PK1179&gs_lcrp=EgZjaHJvbWUyBggAEEUYOTITCAEQLhivARjHARiRAhiABBiKBTIHCAIQABiABDIGCAMQABgeMgYIBBAAGB4yBggFEEUYPDIGCAYQRRg8MgYIBxBFGDzSAQg2MzMyajBqN6gCALACAA&um=1&ie=UTF-8&fb=1&gl=pk&sa=X&geocode=KSuEDX0AP7M-MXhjw6xY1AQ7&daddr=Town+MUNICIPAL+CORPORATION+JINNAH+First+Floor+CMO+Office+Secretariat+Chowrangi,+Mufti+Ahmed+Ur+Rehman+Rd,+near+Tayyaba+Masjid,+Amil+Colony,+Karachi


Staff List
Designation	Name	Contact
HRM	MUHAMMAD OWAIS ABBASI	0300-9256805
LAND	MUHAMMAD NAEEM KHAn	0343-3148831
ADVERTISEMENT	FAHEEM RAZA SHAIKH	0334-3352777
ACCOUNTS	MIMRAN SIYAL	0300-3296080
INFORMATION	RASHID ANSARI	N/A
EDUCATION	MUSHTAQ SOMROO	0300-9786364
ANTI ENCROACHMENT	FAHAD MUSTAFA	0316-2666363
AUDIT	MUHAMMAD AMAN QURASHI	0300-2633744
SENIOR ACCOUNTS	ABDUL NAEEM	N/A
EDUCATION	AYOUB JUZBANIL	N/A
ASSISTANT EXECUTIVE ENGINEER B & R	SHAIKH MUHAMMAD ALAM	0322-2041247
DEPUTY DIRECTOR M & E	ADNAN AHMED	0311-2503245
DEPUTY DIRECTOR M & E	REHAN JABBAR	N/A
DIRECTOR SANITATION	AFTAB ALAM	0317-8394455
DIRECTOR PURCHASE	ZAHID IQBAL	0334-2701549





Union Councils List
S.No	UC	Name of UC	Chairman / Vice Chairman	Contact No
1	UC-01	Fareed Colonys	Muhammad Kamran (Vice Chairman)	0300-2544710
2	UC-02	Haryana Colony	Abdullah Baloach (Town Vice Chairman)	0312-215 0345
3	UC-03	Bismillah Colony	Asif Rehman (Vice Chairman)	0345-1992508
4	UC-04	Islam Nagar	Abid Shah (Vice Chairman)	0344-2606660
5	UC-05	Mominabad	Malik Arif Awan (Town Chairman)	0300-9276565
6	UC-06	Frontier Colony	Mukhtiar Shah (Vice Chairman)	0331-2107024
7	UC-07	Banaras Colony	Dr. Kabeer (Vice Chairman)	0333-2280585
8	UC-08	Peerabad	Nadir ur Rehman (Vice Chairman)	0311-2977133
9	UC-09	Qasba Colony	Syed Baseer Uddin (Vice Chairman)	0344-5532288



Chairman: Maalik Arif


TMC Korangi**
Areas: Korangi, Landhi (some parts), industrial zones.
Website: https://tmckorangi.gos.pk/
Email: tmckorangi@gmail.com
Phone: 021-99333929
Address: ST-1/3, Sector 41/B, Korangi 2-½ Near Chiniot General Hospital Korangi Karachi Karachi City, Sindh
Map location: https://www.google.com/maps/dir/24.8676352,67.059712/PYSDP+TMC+Korangi,+R4FR%2BMM8+Sector+33%2FA,+Main+road,+2+1%2F2+Sector+33+A+Korangi,+Karachi,+74900,+Pakistan/@24.8432636,67.016052,12z/data=!3m1!4b1!4m9!4m8!1m1!4e1!1m5!1m1!1s0x2fbb6e450a526a71:0x9090ebb7b16dfa6c!2m2!1d67.1416875!2d24.8241875?entry=ttu&g_ep=EgoyMDI2MDkwOS4wIKXMDSoASAFQAw%3D%3D

Staff List
Designation	Name	Contact
DIRECTOR INFORMATION	MUHAMMAD AAMIR	0333-2188860
DIRECTOR ANTI ENCROACHMENT/ LOCAL TAX	MUHAMMAD KASHIF	N/A
DIRECTOR INTERNAL AUDIT	SYED SADAT HUSSAIN HASHMI	0300-3698829
SENIOR ACCOUNTS OFFICER	SHAHZAIB	N/A
DIRECTOR SANITATION	MUHAMMAD YOUNUS	0311-8619533
EXECUTIVE ENGINEER (M&E)	ANWER SHAH	0300-2899038
DIRECTOR ADMINISTRATION	ASIF AHMED ABBASI	N/A
DIRECTOR SECURITY	MUHAMMAD AAMIR	N/A
DIRECTOR COMPLAINT CELL (CCIS)	HAQ NAWAZ KHEMTIO	N/A
DEPUTY DIRECTOR PURCHASE	SHAH ALAM	N/A
DIRECTOR CHARGE PARKING	MUHAMMAD SALEEM	N/A
DIRECTOR COUNCIL	MUHAMMAD TOUHEED CHOHAN	0333-8292089
CHIEF MEDICAL OFFICER	DR. AYAZ AWAN	0333-1330159
WELFARE	MIRZA FAHEEM BAIG	0333-2312193
XEN BUILDING & ROADS	QAISAR AZIZ	N/A
DIRECTOR PLANNING	MUHAMMAD ATHER KHAN	N/A
DIRECTOR TRADE LICENCE	ZAFAR KHALID	0301-8214242
DIRECTOR EDUCATION/PAYROLL	SHUJAT HUSSAIN	N/A
DIRECTOR PARKS	MOIN JUTT	N/A
DIRECTOR LAW	REHMAT IQBAL	N/A
DIRECTOR (Urban Immoveable Property Tax)	MUHAMMAD YOUNUS	-
DIRECTOR ADVERTISEMENT	WALI QURESHI	N/A
DEPUTY DIRECTOR LIBRARY	MUHAMMAD WAQAS	N/A


Union Councils List
S.No	UC	Name of UC	Chairman / Vice Chairman	Contact No
1	UC-01	Qayyumabad	Naveed Rehman / Zareen Awan (Town Vice Chairman)	0300-2627443 / 0300-9208450
2	UC-02	Makhdoom Bilawal	Muhammad Israr / Muhammad Naeem Shaikh, ( Town Chairman)	0306-2089168
3	UC-03	Nasir Colony	Qaisar Manzoor / Saied Feroz	0321-2042102
4	UC-04	Zia Colony	Salman Rind / Asghar Ali	0313-3405041
5	UC-05	Sector 33	Amjad / Waseem Haris	/ 0302-1952354
6	UC-06	Korangi	Akhtar Hussain / Muhammad Haroon	0346-2930690
7	UC-07	Rahim Abad	Khursheed / Muhammad Saleem	0322-2258565
8	UC-08	Madina Colony	Ahmed Raza / Umer Farooq	0321-355145
9	UC-09	Ittihad Colony	Muhammad Ashraf / Riaz	0311-3717359 / 0310-1175656
10	UC-10	Ghousia Colony	Habibullah / Adnan Mughal	0313-2748777
11	UC-11	Chakra Goth	Murtuza Ali Memon / Ghafoor Alam Saifi	0312-2689612



Chairman TMC Korangi: MUHAMMAD NAEEM SHEIKH


     ## 3. District Karachi Central



### TMC North Nazimabad
Areas: North Nazimabad, parts of Orangi, Manghopir vicinity.
Website: tmcnorthnazimabad.gos.pk
Email: info@tmc-nn.gos.pk
Phone: 0213-99260366
Address: TMC North Nazimabad,ST-04,Nazim Street,Block-A,North Nazimabad,Opposite Sindh Rangers Hospital District Central Karachi.
Map Location: N/A


Staff List
Designation	Name	Contact
Assistant Director-Local Funds and Audit	Adeel Khan	0334-7364299
Accounts Officer	Imran Ahmed	0321-2916043
Director-Education	Iqbal Siddiqui	0300-2259976
Assistant Executive Engineer(M&E)	Noman Hussain	0333-3013551
Assistant Executive Engineer(B&R)	Muhammad Khalid	0333-3001394
Director-Sanitation	Zaki Haider	0336-2294940
Purchase Officer	Zaki Haider	0336-2294940
Director-Taxation	Syed Muhammad Haris	0345-3176244


Union Councils List
S.No	UC	Name of UC	Chairman / Vice Chairman	Contact No
1	U.C 1	Sir Syed	Arshad Hassan	0333 2188952
2	U.C 2	Farooq-e-Azam	Farhan Sohail	03332254434
3	U.C 3	Siddiq-e-Akber	Maaz Hanfi	03212320015
4	U.C 4	Buffer Zone	Zeeshan	03002353278
5	U.C-5	Taimuria	Onaib	0333-2197718
6	U.C 6	Sakhi Hassan	Mujahid	03008243806
7	U.C-7	Hydairy	Khalid	03340710102
8	U.C 8	Al-Falah	Zulfiqar	03212114939
9	U.C 9	Pharganj	Majid	03028226118
10	U.C 10	Mustafabad	Rehan	03181923997



Chairman: Atif Ali Khan





### TMC Nazimabad
Areas: Nazimabad, Buffer zones, parts of North Nazimabad.
Website: tmcnazimabad.gos.pk 
Email: tmcnazimabad25@gmail.com
Phone: (021) 99260342
Address: Sharah-e-Ibn-e-Sina Road Nazimabad#2 Near Gujjar Nala TMC office Nazimabad Karachi
Map Location: https://www.google.com/maps/dir//W29J%2BQW4+WorkShop+TMC+Nazimabad,+Nazimabad+No.+4+Block+4+Nazimabad,+Karachi,+74600/data=!4m6!4m5!1m1!4e2!1m2!1m1!1s0x3eb33f002dda0f41:0x7698035ef31911aa?sa=X&ved=1t:57443&ictx=111


Staff List
Designation	Name	Contact
DIRECTOR HRM/ADMIN	ZAHEER AHMED	0316-5356252
DIRECTOR INFORMATION	FAHIM MUSTAFA ZAIDI	0300-2290488
ANTI ENCROACHMENT	RASHID KAMAL	0316-1003672
ACCOUNTS OFFICER	ALI HASSAN KAZMI	0313-2229915
DEPUTY DIRECTOR EDUCATION	NAVAID HABIB	0333-3620360
AEE B & R	SIRAJ UDDIN	0300-2190941
AEE M & E	MASROOR	0314-2361334
DIRECTOR SANITATION	ZAHEER AHMED	0321-2452388
DIRECTOR PARKS	ASIF HUSSAIN	0300-9271737
IN-CHARGE SPORTS & CULTURE	USMAN GHANI	0321-2168116
DEPUTY DIRECTOR IT & PAYROLL	HAMMAD ALI	0345-2442016
COUNCIL OFFICER	OBAID ALAM	0300-2154450
DEPUTY DIRECTOR TRADE & WATER TAX	RASHID IQBAL	0332-2169095
DEPUTY DIRECTOR ADVERTISEMENT	TALAT MAHBOOB	0322-9299990
DEPUTY DIRECTOR PROPERTY TAX	MOEEN HASHMI	0312-2018125



Union Councils List
S.No	UC	Name of UC	Chairman / Vice Chairman	Contact No
1	UC-01	Paposh Nagar Near Allama Iqbal School	Aftab Hameed (Vice Chairman)	0302-8259212
2	UC-02	Abbasi Hospital Near Noor ul Islam Masjid	Ali Hassan Shaikh (Vice Chairman)	0336-8221398
3	UC-03	Hadi Market Nazimabad	Muhammad Saleem (Vice Chairman)	0322-6164943
4	UC-04	Nazimabad No.1 Near Agha Juice Center	Syed Muhammad Muzafar (Vice Chairman/Town Chairman)	0346-3149195
5	UC-05	Rizvia Society	Abdul Latif (Vice Chairman)	0311-3000295
6	UC-06	Firdous Colony Near Lal Masjid	Abdul Khaliq (Vice Chairman)	0333-3396207
7	UC-07	Rizvia Imam Bargah Gulbahar No 2	Furqan Islam (Vice Chairman)	0335-3466485



Chairman: Syed Muhammad Muzzaffar


TMC New Karachi**
Areas: New Karachi Town, parts of North Karachi.
Website: https://tmcnewkarachi.gos.pk/
Email: tmcnewkarachi@gmail.com
Phone: N/A
Address: ST-01 SECTOR 11-I, BEHIND TELEPHONE EXCHANGE, NEW KARACHI- KARACHI, POSTAL CODE# 75850.
Map Location: https://www.google.com/maps/dir/24.8676352,67.059712/New+Karachi+Town+Office,+DMC+CENTRAL.+NKZ,+LS+1+ST+1,+Sector-11-I+Sector+11+I+North+Karachi,+Karachi,+Pakistan/@24.918147,66.9952562,12z/data=!3m1!4b1!4m9!4m8!1m1!4e1!1m5!1m1!1s0x3eb341e9df3b6683:0xcd224d7a3362cc28!2m2!1d67.0668938!2d24.9736796?entry=ttu&g_ep=EgoyMDI2MDkwOS4wIKXMDSoASAFQAw%3D%3D


Union Councils List
S.No	UC	Name of UC	Chairman / Vice Chairman	Contact No
1	Union Council No: 01	Shahnawaz Bhutto Colony	(Chairman) Muhammad Naveed (Vice Chairman) Sohail Mubarak	0300-8995331, 0300-7001714
2	Union Council No: 02	Gulshan-e-Saeed	(Chairman) Muhammad Ahmer Khan (Vice Chairman) Faizan Qureshi	0312-2762575, 0311-6625907
3	Union Council No: 03	Khawaja Ajmeer Nagri	(Chairman) Muhammad Ali Arshad (Vice Chairman) Shoaib Bin Zaheer	0332-3643343, 0317-2787071
4	Union Council No: 04	Mustafa Colony	(Chairman) Muhammad Amir (Vice Chairman) Mir Balaj	0315-2591014, 0314-2177815
5	Union Council No: 05	Kala School	(Chairman) Atta Ur Rehman (Vice Chairman) Tauqeer Khan	0335-7193357, 0313-2466267
6	Union Council No: 06	Khamiso Goth	(Chairman) Rehmat Ali (Vice Chairman) Afzal Brohi	0313-2039783, 0314-2210548
7	Union Council No: 07	Madina Colony	(Chairman) Abdul Ghaffar Chishti (Vice Chairman) Adnan Razi	0332-2134028, 0313-3983075
8	Union Council No: 08	Shah Faisal	(Chairman) Khalid Mehmood (Vice Chariman) Imran Faqeer	0319-2134028, 0334-2221148
9	Union Council No: 09	Abu Zar Ghaffari	(Chairman) Muhammad Abbas Shaikh (Vice Chairman) Hanzalah Anwar	0300-2757491, 0313-3549380
10	Union Council No: 10	Godhra	(Chairman) Faisal Ahmed (Vice Chairman) Owais Essa	0310-9218352, 0345-2732714
11	Union Council No: 11	Hakeem Ahsan	(Chairman) Muhammad Ghazanfar Ali (Vice Chairman) Imran Shafiq	0324-2874737, 0332-3129570
12	Union Council No: 12	Kalyana	(Chairman) Azeem Anwar (Vice Chairman) Shahzad Ahmed	0341-1284040, 0321-2727989
13	Union Council No: 13	Muhammad Shah	(Chairman) Shahzaib Satti (Vice Chairman) Jawad Hasan	0314-4116111, 0300-7037676




Chairman, New Karachi Town: Muhammad Yousuf 


### TMC Manghopir
Areas: Manghopir, parts of North Nazimabad/Orangi.
Website: https://tmcmangopir.gos.pk/
Email: N/A
Phone: 0321 1450314
adress:  X2QR+78R, New Karachi Town, Karachi
map location: https://www.google.com/maps/place/Manghopir+Town+Office+(TMC+Manghopir)/@24.9884839,67.040917,693m/data=!3m2!1e3!4b1!4m6!3m5!1s0x3eb341006ad9ecc3:0x90262a208980b5f0!8m2!3d24.9884839!4d67.040917!16s%2Fg%2F11wq7kgbtw!5m1!1e2?entry=ttu&g_ep=EgoyMDI2MDkwOS4wIKXMDSoASAFQAw%3D%3D

Union Councils List
S.No	UC	Name of UC	Chairman / Vice Chairman	Contact No
1	UC-09	Khuda Ki Basti	IRSHAD MUGHAL / ALI HASSAN BROHI	0313-3247986
2	UC-08	Bhatti Goth	QADIR BUX BROHI / RANA M ARIF	0315-2844092
3	UC-07	K.D.A Flats	UMEED ALI QAZI / HAFIZ IMRAN	0313-9251311
4	UC-06	Raheem Goth	ZUBAIDA IQBAL / NOOR MOHD	0307-2276807
5	UC-05	Yousuf Goth	ASHRAF / MUSHTAQ	0312-2922365
6	UC-04	Surjani Town	ATIF HAYAT / JIBRAN	0313-3942737
7	UC-03	Pakhtoonabad	MUFTI KHALID / FAZAL MOLA	0305-1312012
8	UC-02	Manghoopir	YOUNUS MENGAL / ALI AKBER KACHELO	0317-2181605
9	UC-01	Mai Garhi	SALEEM BROHI / HAJI NAWAZ ALI BROHI	0316-2689769
10	UC-10	Liyari Expressway Resettlement Project	MUSTAFA BADAR / MALIK AMIR	0311-6351306
11	UC-11	Hassan Goth	MOR KHAN / QARI ASHRAF	0333-9234816
12	UC-12	Gulshan-e-Mayman	SYED HILAL REHMANI / SAQIB	0332-2202181
13	UC-13	Mullah Hussain Brohi	ANWER BROHI / ALI AKBAR BROHI	0314-2988310
14	UC-14	Kunwari Colony	MUNEEB / HABIBULLAH SAWATI	0300-2063034
15	UC-15	M.P.R Colony	DR AZIZ / NISAR	0347-2564942
16	UC-16	Gabool Colony	HAJI JUMMAN DARBAN/HASSAN BALOCH	0302-2462076


Staff List
Designation	Name	Contact
DIRECTOR HRM (COUNCIL)	MUHAMMAD QADEERUDDIN	0316-2262294
AUDIT OFFICER	HAFIZ MUHAMMAD SHOAIB	0324-2664250
SENIOR ACCOUNTS OFFICER	FAYYAZ CHANNA	0302-3630677
DIRECTOR EDUCATION	SURAYA ABID REHMANI	0313-2826100
SUPERINTENDING ENGINEER B&R	ZULFIQAR JATOI	0000-0000000
ASSISTANT EXECUTIVE ENGINEER M&E	FAHAD JAMEEL KAKEPOTO	0346-8405724
DEPUTY DIRECTOR SANITATION	MUNAWAR SUBA	0300-2579078
DIRECTOR PURCHASE	MUJTABA KHAN	0300-2144122
DIRECTOR ADVERTISEMENT	HAFIZ MUHAMMAD SHOIB	0324-2664250
DIRECTOR BUDGET	AHSAN ALI SHAH	0314-3808135
DIRECTOR INFORMATION	ASAD SIDDIQUI	0322-2383797
DIRECTOR LAND	SURAYA ABID REHMANI	0315-8213710
DIRECTOR PARKS	AFALQ ALAM	0325-2123290
DIRECTOT TAXES	ABDUL GHAFFAR JATOI	0334-2771783
DIRECTOR INFORMATION	ASAD SIDDIQUI	0322-2383797
DIRECTOR EXISE PROPERTY TAX	ASAD SIDDIQUI	0322-2383797
INCHARGE WATER SUPPLY SCHEME	ABDUL GHAFFAR JATOI	0334-2771783
INCHARGE MEDICAL	DANIYAL HUSSAIN	0304-2222889
INCHARGE INDUSTRIAL HOMES	ASLAM BROHI	0345-2047055
ASST DIR CHARGED PARKING	AMEER ALI BROHI	0321-2950711
INCHARGE IT & PAYROLL	SHAD MUHAMMAD	0300-9231641



### TMC Malir
Areas: Malir City, Malir Cantt vicinity, Model Colony (some parts).
Website: https://tmcmalir.gos.pk/
Email: TMCMalir@gmail.com
Phone: N/A
adress: V5PV+6PR, Saudabad Ghazi Dawood Brohi Goth, Karachi
map location: https://www.google.com/maps/place/Union+Committee+No+3+Jaffar+e+Tayyar+TMC+Malir+Karachi/@24.8854404,67.1940387,694m/data=!3m2!1e3!4b1!4m6!3m5!1s0x3eb337004c8f6d9d:0x566406067cf276c!8m2!3d24.8854404!4d67.1940387!16s%2Fg%2F11nvb79gr0!5m1!1e2?entry=ttu&g_ep=EgoyMDI2MDkwOS4wIKXMDSoASAFQAw%3D%3D




Union Councils List
S.No	UC	Name of UC	Chairman / Vice Chairman	Contact No
1	01	Gharibabad	Muhammad Riaz Baloch	03012251750
2	03	Jaffar e tayar	Syed Raza Husssain Rizvi	03462757113
3	05	Qaidabad	Saeed ullah	03002974601
4	06	Dawood Chorangi	Abdullah khan Raja Khailq	03112492701
5	07	Future Colony	Iqbal Din	03332211928
6	08	Sharafi Goth	Nizar Muhammad Baloch	03369259212
7	09	Bakhtawar Goth	Muhammad Ibrahim Soomro	03122784884
8	10	Bhittaiabad	Allah Dino Soomro	03008218959
9	04	Khuldabad	Syed Muzammil Shah (vice Chairman)	03132120719
10	02	Dawood Goth	Jan Muhammad Baloch (Chairman)	03002245377



Staff List
Designation	Name	Contact
As Executive Engineer (M&E)	Fayyaz Ali Domki	03337988808
Director Vigilance	Peer Muhammad	033322112419
Team Leader (Disaster)	Ikram Ullah Khan	03412067786
Director Estate	Muhammad Arsalan	03111033411
Director (Charge Parking)	Arz Muhammad Abbasi	03003566410
Director ( Library)	Muhammad Kamran	03128676230
Director (Internal Audit Officer)	Muzaffar Uddin Shaikh	03352131770
Security Officer	Abdul Shakoor Baloch	03312477800
Director ( Advertisement)	Abdul Hafeez Baloch	03092460563
Director (Social Welfare)	Muhammad Faraz	03452420031
Complain System Officer	Ammar Khan Khilji	03332386677
Budget Officer	Khurram Hafeez	03337141226
Law Officer (Legal Affair)	Abdul Hafeez Sheikh	03313078051
Director (Education)	Jamal Nasir	03152705858
Sport Officer	Muhammad Idrees Kandhro	03002661178
Information Officer	Muhammad Saleem Khan	03432795373
Executive Engineer (B&R)	Faisal Ullah Sheikh	03048712923
Director (Taxes)	Masroor Alam Shah	03171007996
Director (Council)	Adeel Ahmed Gopang	03343871624
Director (Parks & Recreation)	Sohail Ahmed Mughal	03334722872
Director (Sanitation/Anti-Encroachment)	Gul Khan Naseer	03009254999
Chief Medical Officer	Zakia Bhutto	03305531386
Deputy Director (Social Safeguard Click)	Ayesha Mughal	03331911136
Purchase Officer	Muhammad Adnan	0308980333
Director (HRM)	Muhammad Sharif Rahpoto	03330371558
Senior Account Officer	Javaid Ali Abro	03360022910
Superintending Engineer (B&R/M&E)	Rehmat Ullah Memon	03002180110



### TMC Bin Qasim
Areas: Bin Qasim Town, Port Qasim area, Pipri, Gadap (some parts).
Website: https://tmcibrahimhydri.gos.pk/
Email: tmcibrahihyderi48@gmail.com
Phone: N/A
Adress:  V79R+GR4, National Highway - Super Highway Link Road, Razzaqabad Bin Qasim Town, Karachi
Map location: https://www.google.com/maps/place/TMC+Ibrahim+Hyderi/data=!4m2!3m1!1s0x0:0xb32de4c3a2a2092e?sa=X&ved=1t:2428&ictx=111



Union Councils List
S.No	UC	Name of UC	Chairman / Vice Chairman	Contact No
1	Union Committee No. 09	Chashma	Mr. Nazeer Ahmed Bhutto (Elected Town Chairman PPPP)	0300-9235141
2	Union Committee No. 10	Rehri	Mr. Muhammad Rafiq Dawood Jat (Elected Vice-Chairman PPPP)	0321-7052799
3	Union Committee No. 01	Chowkandi	Mr. Manzoor Ahmed Arfani (Elected Member PPPP)	0300-8994688
4	Union Committee No. 02	Shah Lateef	Mr. Muhammad Ameen Jutt (Elected Member-Jamat-e-Islami)	0301-2236812
5	Union Committee No. 03	Cattle Colony	Mr. Abdul Sattar Himayatti (Elected Member PPPP)	0333-2307719
6	Union Committee No. 04	Majeed Colony	Mr. Saif Kamil (Elected Member of PPPP)	0344-4550881
7	Union Committee No. 05	Muzzaffarabad	Mr. Muhammad Yousuf (Elected Member of PTI)	0304-1262009
8	Union Committee No. 06	Muslimabad	Dr. Muhammad Fazal (Elected Member of PTI )	0348-2283800
9	Union Committee No. 07	Sher Pao colony	Mr. Naeem Abbasi (Elected Member of PPPP )	0332-3636361
10	Union Committee No. 08	Ibrahim Hyderi	Mr. Jan Alam Jamot (Elected Member of PPPP)	0342-2074212
11	Union Committee No. 11	Ali Akber Shah	Mr. Muhammad Azeem Baloch (Elected Member of PPPP)	0312-2831926
12	Union Committee No. 01	Chowkandi	Mr. Akhtar Yousuf Arfani (Members Reserved Seats (LABOUR) PPPP).	0323-3282789
13	Union Committee No. 01	Chowkandi	Mr. Hashim Lashari (Members Reserved Seats. DISABLE (PPPP)	0300-3 773060
14	Union Committee No. 04	Majeed Colony	Mst. Shamim Imran (Members Reserved Seats Women (PPPP)	0342-3891322
15	Union Committee No. 05	Muslimabad	Mst. Naheed (Member Reserved Seats Women (PTI)	0321-8201852
16	Union Committee No. 06	Muslimabad	Mr. Hazrat Ali Gujjar (Member Reserved Seats Youth (PPPP)	0333-3035S04
17	Union Committee No. 07	Sher Pao colony	Mst. Umm-E-Habiba (Member Reserved Seats WOMEN (PPPP)	0304-1233896
18	Union Committee No. 08	Ibrahim Hyderi	Mst. Fatima Majeed (Member Reserved Seats. WOMEN (PPPP)	0335-3768462
19	Union Committee No. 08	Ibrahim Hyderi	Mr. Mukhi Odha Mall (Member Reserved Seats. MINORITY (PPPP)	0333-2182140
20	Union Committee No. 08	Ibrahim Hyderi	Mr. Dawood Faqeer (Member Reserved Seats (PPPP)	0314-2557658



### TMC Gadap
Areas: Gadap Town, rural/peri-urban Malir.
Website: https://tmcgadap.gos.pk/
Email: tmcgadapofficial@gmail.com
Phone: 021-99232593
Adress: Plot no, Street 5, Block 14 Gulshan-e-Iqbal, Karachi
map location: https://www.google.com/maps/place/Camp+office+tmc+gadap+town/data=!4m2!3m1!1s0x0:0x18a44d88a487d7d3?sa=X&ved=1t:2428&ictx=111



Staff List
Designation	Name	Contact
DIRECTOR (HRM)	HUMAYUN IMRAN KHAN	0300-2578188
CHIEF MEDICAL OFFICER	DR. LUBNA MAQBOOL	0
CHIEF ACCOUNTS OFFICER	IFTIKHAR AHMED DAYO	0300-8907336
DIRECTOR TAXES	HUMAYUN IMRAN KHAN	0300-2578188
SUPERINTENDING ENGINEER (B&R)	FARMAN ALI LASHARI	0333-7502852
SUPERINTENDING ENGINEER (M&E)	NOOR AHMED JAMALI	0301-3810122
DIRECTOR ESTABLISHMENT	LAL JAN NABI	0321-3607006
DIRECTOR PROPERTIES	SOHAIL YAR KHAN	0318-2991284
DIRECTOR PRINT MEDIA & PUBLICITY (INFORMATION)	RIZWAN UR RASHEED	0321-2271149
DIRECTOR PENSION	JALAL HANIF	0333-2388705
DIRECTOR INTERNAL AUDIT	KASHIF IQBAL	0333-3441439
DIRECTOR (SOCIAL WELFARE)	JAHANGIR SHAIKH	0300-2140878
DIRECTOR ADVERTISEMENT	HIDAYAT ULLAH SHAIKH	0301-2505558
DIRECTOR (SANITATION & SOLID WASTE MANAGEMENT)	MUHAMMAD ASHRAF BALOCH	0320-9900990
DIRECTOR WATER RESOURCES MANAGEMENT	JAMEEL MEMON	0324-2040525
DIRECTOR CONTRACT MANAGEMENT	ABDUL NASIR BALOCH	0307-0253997
DIRECTOR BUDGET	TOUFEEQ JOKHIO	0312-2692317
MOTOR VEHICLE IN-CHARGE	ABDUL GHAFFAR MIRANI	0312-1080739
LAW OFFICER	SOHAIL HAMEED KAZI	0321-9284781
DIRECTOR PARKS	MUHAMMAD AFZAL LAGHARI	0335-0277170
P.S TO CHAIRMAN	LAL JAN NABI	0321-3607006
DIRECTOR SPORTS & CULTURE	TAHIR BALOCH	0324-2912774





### TMC Ibrahim Hyderi
Areas: Ibrahim Hyderi, Rehri, coastal villages.
Website: https://tmcibrahimhydri.gos.pk/
Email: tmcibrahihyderi48@gmail.com
Phone: N/A
adress: V79R+GR4, National Highway - Super Highway Link Road, Razzaqabad Bin Qasim Town, Karachi
map location: https://maps.app.goo.gl/CHQwnWunm4hrNpM26



Union Councils List
S.No	UC	Name of UC	Chairman / Vice Chairman	Contact No
1	Union Committee No. 09	Chashma	Mr. Nazeer Ahmed Bhutto (Elected Town Chairman PPPP)	0300-9235141
2	Union Committee No. 10	Rehri	Mr. Muhammad Rafiq Dawood Jat (Elected Vice-Chairman PPPP)	0321-7052799
3	Union Committee No. 01	Chowkandi	Mr. Manzoor Ahmed Arfani (Elected Member PPPP)	0300-8994688
4	Union Committee No. 02	Shah Lateef	Mr. Muhammad Ameen Jutt (Elected Member-Jamat-e-Islami)	0301-2236812
5	Union Committee No. 03	Cattle Colony	Mr. Abdul Sattar Himayatti (Elected Member PPPP)	0333-2307719
6	Union Committee No. 04	Majeed Colony	Mr. Saif Kamil (Elected Member of PPPP)	0344-4550881
7	Union Committee No. 05	Muzzaffarabad	Mr. Muhammad Yousuf (Elected Member of PTI)	0304-1262009
8	Union Committee No. 06	Muslimabad	Dr. Muhammad Fazal (Elected Member of PTI )	0348-2283800
9	Union Committee No. 07	Sher Pao colony	Mr. Naeem Abbasi (Elected Member of PPPP )	0332-3636361
10	Union Committee No. 08	Ibrahim Hyderi	Mr. Jan Alam Jamot (Elected Member of PPPP)	0342-2074212
11	Union Committee No. 11	Ali Akber Shah	Mr. Muhammad Azeem Baloch (Elected Member of PPPP)	0312-2831926
12	Union Committee No. 01	Chowkandi	Mr. Akhtar Yousuf Arfani (Members Reserved Seats (LABOUR) PPPP).	0323-3282789
13	Union Committee No. 01	Chowkandi	Mr. Hashim Lashari (Members Reserved Seats. DISABLE (PPPP)	0300-3 773060
14	Union Committee No. 04	Majeed Colony	Mst. Shamim Imran (Members Reserved Seats Women (PPPP)	0342-3891322
15	Union Committee No. 05	Muslimabad	Mst. Naheed (Member Reserved Seats Women (PTI)	0321-8201852
16	Union Committee No. 06	Muslimabad	Mr. Hazrat Ali Gujjar (Member Reserved Seats Youth (PPPP)	0333-3035S04
17	Union Committee No. 07	Sher Pao colony	Mst. Umm-E-Habiba (Member Reserved Seats WOMEN (PPPP)	0304-1233896
18	Union Committee No. 08	Ibrahim Hyderi	Mst. Fatima Majeed (Member Reserved Seats. WOMEN (PPPP)	0335-3768462
19	Union Committee No. 08	Ibrahim Hyderi	Mr. Mukhi Odha Mall (Member Reserved Seats. MINORITY (PPPP)	0333-2182140
20	Union Committee No. 08	Ibrahim Hyderi	Mr. Dawood Faqeer (Member Reserved Seats (PPPP)	0314-2557658





   
### TMC Landhi
Areas: Landhi, Korangi industrial zones.
Website: https://tmclandhi.gos.pk/
Email: tmclandhi@gmail.com
Phone: 02199333979
adress: R5PJ+WFH, Sector 36 E Landhi Town, Karachi
map location: https://www.google.com/maps/place/Town+Municipal+Corporation+Landhi/data=!4m2!3m1!1s0x0:0x3c91f8c9ffc0e1d0?sa=X&ved=1t:2428&ictx=111


Union Councils List
S.No	UC	Name of UC	Chairman / Vice Chairman	Contact No
1	UC-01	Labor Squre	Noor Hussain (Chairman)	0333-2127428
2	UC-02	Zaman Town	Muhammad Qasim Khan (Chairman)	0304-2141034
3	UC-03	Shareef Colony	Nadir Ali Khan Lodhi (Chairman)	0333-2214923
4	UC-04	Kawaja Ajmeer	Muhammad Idrees ( Chairman)	0300-2557619
5	UC-05	Bhutto Nagar	Sarfraz Ahmed (Chairman)	0315-2658986
6	UC-06	Farooq Villas	Israr Ahmed Siddiqui (Chairman)	0312-2125185
7	UC-07	Zaman Abad	Mirza Farhan Baig (Chairman)	0333-2145770
8	UC-08	Musarat Mohani Colony	Muhammad Ayub Abbasi (Chairman)	0311-3311539
9	UC-09	Nizam -e- Mustafa Colony	Hafiz Anwar Elahi (Chairman)	0316-0018107
10	Uc-10	100 Quarter	Abdul Hafeez (Chairman)	0346-8225488


Staff List
Designation	Name	Contact
HRM	MUHAMMAD OWAIS ABBASI	0300-9256805
LAND	MUHAMMAD NAEEM KHAn	0343-3148831
ADVERTISEMENT	FAHEEM RAZA SHAIKH	0334-3352777
ACCOUNTS	MIMRAN SIYAL	0300-3296080
INFORMATION	RASHID ANSARI	N/A
INFORMATION TECHNOLOGY	SYED SHAHID	0333-2295816
ANTI ENCROACHMENT	FAHAD MUSTAFA	0316-2666363
AUDIT	NAEEM GOHAR	0321-2202199
SENIOR ACCOUNTS	ABDUL NAEEM	N/A
EDUCATION	MUSHAHID ANWAR	0333-3593208
ASSISTANT EXECUTIVE ENGINEER B & R	ASIF NAZEER BHURT	0300-2496216
DEPUTY DIRECTOR M & E	ADNAN AHMED	0311-2503245
DEPUTY DIRECTOR M & E	REHAN JABBAR	N/A
DIRECTOR SANITATION	AFTAB ALAM	0317-8394455
DIRECTOR PURCHASE	SYED LAIQ ALI	03332336282




### TMC Shah Faisal
Areas: Shah Faisal Town, Model Colony (some parts), airport vicinity.
Website: https://tmcshahfaisal.gos.pk/
Email: tmc@gmail.com
Phone: (021) 99333576
adress: V42J+9P5, Sector 8 A Korangi Industrial Area, Karachi
map location: google.com/maps/place/TMC+SHAH+FAISAL+OFFICE+(White+House)/data=!4m2!3m1!1s0x0:0x66d0c6d36e543228?sa=X&ved=1t:2428&ictx=111


Union Councils List
S.No	UC	Name of UC	Chairman / Vice Chairman	Contact No
1	UC-01	NATHA KHAN	ATEEQ (CHAIRMAN)	0312-8511688
2	UC-02	AL-FALAH	SAIF UDDIN (CHAIRMAN)	0333-3008176
3	UC-03	GREEN TOWN	CH. ASAD (CHAIRMAN)	0333-2236738
4	UC-04	RETA PLOT	ADNAN (CHAIRMAN)	0315-5924753
5	UC-05	GULZAR COLONY	SALMAN (CHAIRMAN)	0324-3056150
6	UC-06	AWAMI COLONY	YAQOOB KALROO (CHAIRMAN)	0300-3398596
7	UC-07	BILAL COLONY	ABDUL REHMAN (CHAIRMAN)	0301-2568872
8	UC-08	MEHRAN TOWN	ZEESHAN ZAIB (CHAIRMAN)	0301-8103144



Staff List
Designation	Name	Contact
HRM	MUHAMMAD OWAIS ABBASI	0300-9256805
LAND	MUHAMMAD NAEEM KHAn	0343-3148831
ADVERTISEMENT	FAHEEM RAZA SHAIKH	0334-3352777
ACCOUNTS	MIMRAN SIYAL	0300-3296080
INFORMATION	RASHID ANSARI	N/A
EDUCATION	MUSHTAQ SOMROO	0300-9786364
ANTI ENCROACHMENT	FAHAD MUSTAFA	0316-2666363
AUDIT	MUHAMMAD AMAN QURASHI	0300-2633744
SENIOR ACCOUNTS	ABDUL NAEEM	N/A
EDUCATION	AYOUB JUZBANIL	N/A
ASSISTANT EXECUTIVE ENGINEER B & R	SHAIKH MUHAMMAD ALAM	0322-2041247
DEPUTY DIRECTOR M & E	ADNAN AHMED	0311-2503245
DEPUTY DIRECTOR M & E	REHAN JABBAR	N/A
DIRECTOR SANITATION	AFTAB ALAM	0317-8394455
DIRECTOR PURCHASE	ZAHID IQBAL	0334-2701549



### TMC Model Colony
Areas: Model Colony, parts of Malir/Korangi.
Website: https://www.tmcmodelcolony.gos.pk/
Email: tmcmodelcolony2024@gmail.com
Phone: 02199248123
adress: Street No. 2, Saudabad Darakhshan Cooperative Housing Society Kala Board, Karachi
map location: https://www.google.com/maps/place/TMC+Model+Colony/@24.8826007,67.1856977,694m/data=!3m2!1e3!4b1!4m6!3m5!1s0x3eb3390054e4870b:0x52678707ec80584b!8m2!3d24.8826007!4d67.1856977!16s%2Fg%2F11w3d_fy75!5m1!1e2?entry=ttu&g_ep=EgoyMDI2MDkwOS4wIKXMDSoASAFQAw%3D%3D




Union Councils List
S.No	UC	Name of UC	Chairman / Vice Chairman	Contact No
1	UC-01	KHAZMABAD	RAFIQ UDDIN (Chairman)	0333-3155547
2	UC-02	INDUS MEHRAN	WASEEM MIRZA (Chairman)	03332129952
3	UC-03	KHOKRAPAR	ABDUL MOEEZ (Chairman)	0331-2598472
4	UC-04	LIAQUAT MARKET	KAREEM BUKSH (Chairman)	0332-3640671
5	UC-05	KALA BOARD	TALHA KHAN (Chairman)	0321-2248788
6	UC-06	HAJI RAHEEM KHAN JOKIO	SAAD YOUSUF ZAI (Chairman)	0333-7154887
7	UC-07	RAFEE BANGLOW	FARHAN KHAN (Chairman)	0333-2396425
8	UC-08	JAMIYA MILYA	AHSAN ZAMEER (Chairman)	0300-8213042




### TMC Orangi
   
Website: https://tmcorangi.gos.pk/
Email: tmc@orangitown.com
Phone:  0331 2802784
adress:  Near, 11-E 5 Number Chowrangi, Muhammad Nagar Sector 11 E Orangi Town, Karachi, 75800arachi
map location: https://www.google.com/maps/place/Town+Municipal+Corporation+(TMC)+Orangi+Town/@24.941715,66.9977777,693m/data=!3m2!1e3!4b1!4m6!3m5!1s0x3eb36aa9c5c00001:0x8658884bcd0b9619!8m2!3d24.941715!4d66.9977777!16s%2Fg%2F11sscs9x41!5m1!1e2?entry=ttu&g_ep=EgoyMDI2MDkwOS4wIKXMDSoASAFQAw%3D%3D



Union Councils List
S.No	UC	Name of UC	Chairman / Vice Chairman	Contact No
1	UC-01	Fareed Colonys	Muhammad Kamran (Vice Chairman)	0300-2544710
2	UC-02	Haryana Colony	Abdullah Baloach (Town Vice Chairman)	0312-215 0345
3	UC-03	Bismillah Colony	Asif Rehman (Vice Chairman)	0345-1992508
4	UC-04	Islam Nagar	Abid Shah (Vice Chairman)	0344-2606660
5	UC-05	Mominabad	Malik Arif Awan (Town Chairman)	0300-9276565
6	UC-06	Frontier Colony	Mukhtiar Shah (Vice Chairman)	0331-2107024
7	UC-07	Banaras Colony	Dr. Kabeer (Vice Chairman)	0333-2280585
8	UC-08	Peerabad	Nadir ur Rehman (Vice Chairman)	0311-2977133
9	UC-09	Qasba Colony	Syed Baseer Uddin (Vice Chairman)	0344-5532288




Staff List
Designation	Name	Contact
HRM	MUHAMMAD OWAIS ABBASI	0300-9256805
LAND	MUHAMMAD NAEEM KHAn	0343-3148831
ADVERTISEMENT	FAHEEM RAZA SHAIKH	0334-3352777
ACCOUNTS	MIMRAN SIYAL	0300-3296080
INFORMATION	RASHID ANSARI	N/A
EDUCATION	MUSHTAQ SOMROO	0300-9786364
ANTI ENCROACHMENT	FAHAD MUSTAFA	0316-2666363
AUDIT	MUHAMMAD AMAN QURASHI	0300-2633744
SENIOR ACCOUNTS	ABDUL NAEEM	N/A
EDUCATION	AYOUB JUZBANIL	N/A
ASSISTANT EXECUTIVE ENGINEER B & R	SHAIKH MUHAMMAD ALAM	0322-2041247
DEPUTY DIRECTOR M & E	ADNAN AHMED	0311-2503245
DEPUTY DIRECTOR M & E	REHAN JABBAR	N/A
DIRECTOR SANITATION	AFTAB ALAM	0317-8394455
DIRECTOR PURCHASE	ZAHID IQBAL	0334-2701549



### TMC Mominabad

Website: https://tmcmominabad.gos.pk/
Email: mominabadtmc@gmail.com
Phone:  (021) 99333710
adress: W2P5+J3J, Metroville Sector 5 Muhammad Nagar, Karachi
map location: google.com/maps/place/TMC+Mominabad+And+Uc+6+Office/data=!4m2!3m1!1s0x0:0x5a139cc4bcfec242?sa=X&ved=1t:2428&ictx=111


Union Councils List
S.No	UC	Name of UC	Chairman / Vice Chairman	Contact No
1	UC-01	Fareed Colonys	Muhammad Kamran (Vice Chairman)	0300-2544710
2	UC-02	Haryana Colony	Abdullah Baloach (Town Vice Chairman)	0312-215 0345
3	UC-03	Bismillah Colony	Asif Rehman (Vice Chairman)	0345-1992508
4	UC-04	Islam Nagar	Abid Shah (Vice Chairman)	0344-2606660
5	UC-05	Mominabad	Malik Arif Awan (Town Chairman)	0300-9276565
6	UC-06	Frontier Colony	Mukhtiar Shah (Vice Chairman)	0331-2107024
7	UC-07	Banaras Colony	Dr. Kabeer (Vice Chairman)	0333-2280585
8	UC-08	Peerabad	Nadir ur Rehman (Vice Chairman)	0311-2977133



Staff List
Designation	Name	Contact
HRM	MUHAMMAD OWAIS ABBASI	0300-9256805
LAND	MUHAMMAD NAEEM KHAn	0343-3148831
ADVERTISEMENT	FAHEEM RAZA SHAIKH	0334-3352777
ACCOUNTS	MIMRAN SIYAL	0300-3296080
INFORMATION	RASHID ANSARI	N/A
EDUCATION	MUSHTAQ SOMROO	0300-9786364
ANTI ENCROACHMENT	FAHAD MUSTAFA	0316-2666363
AUDIT	MUHAMMAD AMAN QURASHI	0300-2633744
SENIOR ACCOUNTS	ABDUL NAEEM	N/A
EDUCATION	AYOUB JUZBANIL	N/A
ASSISTANT EXECUTIVE ENGINEER B & R	SHAIKH MUHAMMAD ALAM	0322-2041247
DEPUTY DIRECTOR M & E	ADNAN AHMED	0311-2503245
DEPUTY DIRECTOR M & E	REHAN JABBAR	N/A
DIRECTOR SANITATION	AFTAB ALAM	0317-8394455
DIRECTOR PURCHASE	ZAHID IQBAL	0334-2701549

## Verification log

| Field | Status |
|---|---|
| TMC names and supplied contacts | Imported from user-provided file; not independently verified here. |
| Website availability | Supplied URLs retained; confirm they resolve and belong to the named TMC. |
| Emails | Supplied addresses retained; confirm through official pages or a controlled test message. |
| Phones | Supplied numbers retained; call to confirm office/department and current ownership. |
| Map links and addresses | Supplied locations retained; confirm the office pin and address. |
| Staff/UC contacts | Treat as sensitive operational data; verify and restrict access. |

## Suggested machine-readable conversion

Have the data team parse each `### TMC ...` section into JSON with stable IDs such as `tmc_saddar`, `tmc_jamshed`, and `tmc_north_nazimabad`. Keep `source_text`, `last_verified`, and `verification_status` fields for every record.
