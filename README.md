Textes d'Affiches - API
======
Le projet Textes d'Affiches met en relation directe des affiches de films et des livres accesibles sur Gallica, en s'appuyant sur Wikidata, data.bnf.fr et OMDb.  
L'API est accessible à l'adresse suivante : http://zone47.com/tda/api/  
Les données renvoyées sont soit :    
    - des films avec les œuvres littéraires adaptées  
    exemple : api/movie.json  
    - des œuvres littéraires avec leurs adaptations cinématographiques   
    exemple : api/book.json  
Les données sont au format JSON.  
  
* 20 films (avec œuvres adaptées) au hasard  
http://zone47.com/tda/api/  
* 20 livres (avec adaptations) au hasard  
http://zone47.com/tda/api/?type=books  
  
* Recherche textuelles "lieues" pour des films (avec œuvres adaptées)  
http://zone47.com/tda/api/?s=lieues  
* Recherche textuelles "trois" pour des livres  (avec adaptations)  
http://zone47.com/tda/api/?s=trois&type=books  
  
* Films (avec œuvres adaptées) du genre Q319221/"film d'aventure"  
http://zone47.com/tda/api/?genre=319221  
* Films (avec œuvres adaptées) réalisés par Q2001/"Stanley Kubrick"  
http://zone47.com/tda/api/?director=2001  
* Films (avec œuvres adaptées) avec Q23842/"Jean Gabin"  
http://zone47.com/tda/api/?starring=23842  
* Films (avec œuvres adaptées) avec pays Q15180/"URSS"  
http://zone47.com/tda/api/?country=15180  
* Films (avec œuvres adaptées) avec langue Q150/"français"  
http://zone47.com/tda/api/?language=150  
* Films (avec œuvres adaptées) des années 1920-1930  
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

***************** Mise à jour janvier 2017
* article et introduction de Wikipédia pour les films et livres  
Quand l'article existe. Les introductions, qui correspondent à ce qu'il y a avant le sommaire, sont limitées à 500 caractères, parce que parfois c'est très très long. Ils sont dans le paquet json comme avant (propriétés WParticle et WPintro).

* autocomplétion  
Exemple http://zone47.com/tda/api/autocompletion.php?keyword=trois
Cela renvoie de 0 à 5 résultats avec chacun 4 propriétés :
    - "prop", la propriété correspondante
    - "qwd", le numéro Wikidata
    - "label", le libellé
    - "img", éventuellement une URL d'image si disponible pour les films et livres
Les résultats sont classés par occurrences décroissantes. Ainsi "trois" renverra d'abord le livre "Les Trois Mousquetaires" et "William" l'auteur Shakespeare.  

* liste de valeurs par propriétés par occurrences décroissante  
Exemple : http://zone47.com/tda/api/cat.php?cat=genre_book pour avoir les genres de livres et http://zone47.com/tda/api/cat.php?cat=genre_book&limit=10 pour avoir les plus 10 plus importants. Ce classement tient compte des sous-classes; ainsi "roman" comprend notamment les romans policiers et les romans d'aventures. À noter que cette approche, indispensable pour éviter les aberrations, implique nécessairement un peu de bruit pour les genres avec d'éventuelles superclasses qui pourrait être filtrées comme "fiction".  
Les valeurs possibles pour le paramètre "cat" :  
    - "author"
    - "director"
    - "genre_movie"
    - "starring"
    - "language"
    - "country"
    - "genre_book"

* Pour l'autocomplétion et la liste des valeurs par propriété, les données obtenues sont réutilisables comme précédemment. Par exemple du premier résultat de la recherche "trois" on déduit un lien vers le livre, arrivé en premier http://zone47.com/tda/api/?type=books&q=140527 , le film arrivé en second http://zone47.com/tda/api/?q=Q309248 et ainsi de suite. Ou encore pour la liste des réalisateurs http://zone47.com/tda/api/cat.php?cat=director avec le premier résultat on déduit un lien vers http://zone47.com/tda/api/?director=55294