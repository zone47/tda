Textes d'Affiches - API
======
L'API est accessible à l'adresse suivante : http://zone47.com/tda/api/  
Les données renvoyées sont soit   
    - des films avec les œuvres littéraires adaptées  
    exemple : api/movie.json  
    - des œuvres littéraires avec leurs adaptations cinématographiques   
    exemple : api/book.json  
Les données sont au format JSON.  
  
* 20 films (avec oeuvres adaptées) au hasard  
http://zone47.com/tda/api/  
* 20 livres (avec adaptations) au hasard  
http://zone47.com/tda/api/?type=books  
  
* Recherche textuelles "lieues" pour des films (avec oeuvres adaptées)  
http://zone47.com/tda/api/?s=lieues  
* Recherche textuelles "trois" pour des livres  
http://zone47.com/tda/api/?s=trois&type=books  
  
* Films (avec oeuvres adaptées) du genre Q319221/"film d'aventure"  
http://zone47.com/tda/api/?genre=319221  
* Films (avec oeuvres adaptées) réalisés par Q2001/"Stanley Kubrick"  
http://zone47.com/tda/api/?director=2001  
* Films (avec oeuvres adaptées) avec Q23842/"Jean Gabin"  
http://zone47.com/tda/api/?starring=23842  
* Films (avec oeuvres adaptées) avec pays Q15180/"URSS"  
http://zone47.com/tda/api/?country=15180  
* Films (avec oeuvres adaptées) avec langue Q150/"français"  
http://zone47.com/tda/api/?language=150  
* Films (avec oeuvres adaptées) des années 1920-1930  
http://zone47.com/tda/api/?y1=1920&y2=1930  
  
* Livres (avec adaptations) du genre Q858330/"roman d'amour"  
http://zone47.com/tda/api/?type=books&genre=858330  
* Livres (avec adaptations) des années 1880-1900  
http://zone47.com/tda/api/?y1=1880&y2=1900&type=books  
* Recherche des livres (avec adaptations) de Charles Dickens (Q5686)  
http://zone47.com/tda/api/?type=books&author=5686  
  
* Recherche infos et livre(s) adapaté(s) du film avec identifiant Wikidata Q657259/"De grandes espérances"  
http://zone47.com/tda/api/?q=Q657259  
* Recherche infos et livre(s) adapaté(s) du film avec identifiant imdb tt0119223/"De grandes espérances"  
http://zone47.com/tda/api/?imdb=tt0119223  
* Recherche infos et adaptations du livre avec identifiant bnf "cb122246495"/"De grandes espérances"  
http://zone47.com/tda/api/?bnf=122246495

[NB : type a comme valeur par défaut "movies"]  
[NB2 : pour les identifiant wikidata,  le caratère "Q" est facultatif ; ?genre=Q1433443 équivaut à ?genre=1433443]  
[NB3 : hormis pour q, imdb, et bnf, censés ne renvoyer qu'une réponse, les autres paramètres sont combinables pour un même type (books ou movies) même si étant donné le volume actuel cela ne présente pas grand intérêt]  
