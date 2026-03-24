# OntoViz — Visualiseur d'Ontologies OWL 2

## Manuel Technique

### Architecture MVC (PHP 8)

```
ontoviz/
├── index.php                    # Point d'entrée, routing
├── .htaccess                    # URL rewriting Apache
├── composer.json                # Dépendances (EasyRdf)
├── config/
│   └── config.php               # Constantes globales
├── app/
│   ├── core/
│   │   ├── Router.php           # Routeur HTTP GET/POST
│   │   └── Controller.php       # Classe de base (render, json, redirect)
│   ├── controllers/
│   │   ├── HomeController.php   # Accueil + upload form
│   │   ├── OntologyController.php # Upload traitement + vue principale
│   │   └── ApiController.php    # Endpoints JSON pour D3.js
│   ├── models/
│   │   └── OntologyModel.php    # Parsing RDF/OWL via EasyRdf
│   └── views/
│       ├── layouts/main.php     # Layout HTML principal
│       ├── home/
│       │   ├── index.php        # Page d'accueil
│       │   └── upload.php       # Formulaire d'upload
│       └── ontology/
│           └── index.php        # Interface de visualisation
├── public/
│   ├── css/style.css            # Styles (thème sombre)
│   ├── js/viz.js                # Toutes les visualisations D3.js
│   └── assets/
│       └── humans.rdf           # Ontologie exemple
└── data/                        # Fichiers uploadés (chmod 777)
```

### Routes HTTP

| Méthode | URL | Contrôleur | Description |
|---------|-----|-----------|-------------|
| GET | `/` | HomeController@index | Accueil |
| GET | `/upload` | HomeController@upload | Formulaire upload |
| POST | `/upload` | OntologyController@upload | Traitement fichier |
| GET | `/ontology` | OntologyController@index | Interface viz |
| GET | `/api/hierarchy` | ApiController@hierarchy | JSON arbre classes |
| GET | `/api/properties` | ApiController@properties | JSON hiérarchie props |
| GET | `/api/concept` | ApiController@concept | JSON détail concept |
| GET | `/api/radial` | ApiController@radial | JSON radial |
| GET | `/api/progressive` | ApiController@progressive | JSON graphe force |

### Paramètres API

- `?root=URI` — URI du concept racine (optionnel, sinon tout l'arbre)
- `?depth=N` — Profondeur de traversée (défaut 3)

### Formats supportés

`.rdf`, `.owl`, `.xml` (RDF/XML), `.ttl` (Turtle), `.n3`, `.nt` (N-Triples), `.jsonld`

---

## Manuel de Réalisation

### Visualisations implémentées

#### 1. Radiale (◎)
- Concept central au centre, fils sur le 1er cercle, petits-fils sur le 2e, etc.
- Code couleur identique pour tous les descendants d'un même parent
- Zoom/pan avec D3 zoom
- Clic sur un nœud → navigation vers ce concept

#### 2. Coupe / Circle Packing (⬡)
- Hiérarchie représentée par des cercles imbriqués (D3 `pack`)
- Plus le cercle est grand, plus il a de descendants
- Zoomable, cliquable

#### 3. Progressive / Graphe de force (⟶)
- Nœuds positionnés par simulation de forces D3
- Liens hiérarchiques (ligne pleine) vs liens propriétés nommées (tirets + flèche)
- Drag & drop des nœuds
- Affiche les relations `rdfs:domain → rdfs:range`

#### 4. Arbre effondrable (🌳)
- Arbre horizontal D3 `tree`
- Clic sur un nœud pour déplier/replier ses enfants
- Shift+clic → naviguer vers le concept

#### 5. Sunburst (☀)
- Anneau radial par niveaux (D3 `partition`)
- Chaque secteur = un concept, taille proportionnelle au sous-arbre
- Clic pour info + navigation

### Navigation inter-visualisations

L'état de navigation est conservé dans un objet `state` JS :
- `state.currentRoot` — URI du concept courant
- `state.currentViz` — visualisation active
- `state.history` — pile pour le fil d'Ariane

Changer de visualisation (onglets) ne reset pas le concept sélectionné → on peut explorer en radiale puis basculer en coupe sans perdre le contexte.

### Installation

```bash
# Prérequis : PHP 8+, Composer, Apache avec mod_rewrite

# 1. Extraire l'archive
unzip ontoviz.zip -d /var/www/html/ontoviz
cd /var/www/html/ontoviz

# 2. Installer EasyRdf
composer install

# 3. Droits écriture sur data/
chmod 777 data/

# 4. Apache : activer mod_rewrite
a2enmod rewrite
# Dans votre VirtualHost ou .htaccess, AllowOverride All
```

### Dépendances

| Outil | Version | Rôle |
|-------|---------|------|
| PHP | ≥ 8.0 | Serveur |
| EasyRdf | ^1.1 | Parsing RDF/OWL |
| D3.js | 7.8.5 | Visualisations |
| Composer | any | Gestion dépendances |
| Apache | any | Serveur web (mod_rewrite) |

### Ajout d'une visualisation

1. Ajouter un bouton `.tab-btn` dans `ontology/index.php`
2. Ajouter un case dans le `switch` de `render()` dans `viz.js`
3. Créer la fonction `drawMaViz(data)` dans `viz.js`
4. Si nécessaire, créer un endpoint dans `ApiController.php` et une route dans `index.php`
