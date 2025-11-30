Architecture et Déploiement de l'application mini projet ci cd
========================================

Ceci est le README pour le projet qui déploie une application web simple à deux niveaux (**WebApp + Base de Données**) au sein d'un **VPC existant** sur AWS, avec une pipeline **CI/CD automatisée** via GitHub Actions.

Table des Matières
------------------

*   [1\. 🏗️ Architecture – Explication Simple](https://www.google.com/search?q=#1--architecture--explication-simple)
    
*   [2\. 🛠️ Instructions pour Reproduire l'Infrastructure](https://www.google.com/search?q=#2--instructions-pour-reproduire-linfrastructure)
    
*   [3\. 🚀 Pipeline CI/CD – Explication](https://www.google.com/search?q=#3--pipeline-cicd--explication)
    
*   [4\. 📦 Structure Recommandée du Répertoire](https://www.google.com/search?q=#4--structure-recommandée-du-répertoire)
    
*   [5\. 📧 Notifications & Alertes](https://www.google.com/search?q=#5--notifications--alertes)
    

1\. 🏗️ Architecture – Explication Simple
-----------------------------------------

L’architecture déployée utilise une approche standard **Web App + Base de Données** (Tier-2) dans un **VPC (Virtual Private Cloud)** existant.

### Diagramme Conceptuel

Le flux de l'application est structuré comme suit :

*   **Accès Public** : Via l'Internet Gateway (IGW) vers le Subnet Public.
    
*   **Couche Application** : L'**EC2 WebApp (php)** réside dans le **Subnet Public** (accessible ports 80/8080).
    
*   **Couche Données** : L'**EC2 MySQL DB** est isolée dans le **Subnet Privé** (accessible uniquement par la WebApp via le port **3306**).
    
*   **Surveillance** : L'**Agent CloudWatch** collecte les métriques CPU et les logs.
    
*   **Alerte** : Le service **SNS** envoie des notifications (Email/SMS) en cas d'incident.

<img width="1536" height="1024" alt="c67ccae0-6e7d-4ed6-ac95-a9a0a33f3dae" src="https://github.com/user-attachments/assets/4f82005b-1bed-4b1e-853c-f90f15f61795" />

### Web App accessible publiquement

<img width="955" height="144" alt="Screenshot 2025-11-30 154358" src="https://github.com/user-attachments/assets/d8866dbd-07b3-4309-a1f2-e1bab87dac4c" />

  
  

2\. 🛠️ Instructions pour Reproduire l'Infrastructure
-----------------------------------------------------

L'infrastructure est déployée via **AWS CloudFormation**.

### ✔️ 1. Prérequis AWS

Assurez-vous que les ressources AWS suivantes existent :

*   Un **VPC** existant.
    
*   Un **Subnet Public** et un **Subnet Privé** dans ce VPC.
    
*   Une **KeyPair** (fichier .pem).
    
*   Un **Topic SNS** (email ou SMS).
    

### ✔️ 2. Configuration de l'Utilisateur IAM pour GitHub Actions

Créez un utilisateur **IAM** avec les permissions : cloudformation:\*, ec2:\*, ssm:\*, s3:\*.

Ajoutez les clés suivantes comme **Secrets GitHub** :

**Secret GitHubDescription**
AWS\_ACCESS\_KEY\_ID Clé d'accès IAM 

AWS\_SECRET\_ACCESS\_KEY Clé secrète IAM 

AWS\_REGION Région de déploiement AWS (ex: eu-west-3)

EC2\_HOST Adresse publique DNS de l'EC2 WebApp

EC2\_SSH\_KEY Clé privée SSH (pour le CD)

### ✔️ 3. Déployer la Stack CloudFormation

Pour un déploiement manuel initial :

aws cloudformation deploy \    --template-file cloudformation/main.yml \    --stack-name MyInfraStack \    --capabilities CAPABILITY_NAMED_IAM   `

3\. 🚀 Pipeline CI/CD – Explication
-----------------------------------

L'automatisation du développement et du déploiement est gérée par **GitHub Actions**.

### 🎯 CI – Intégration Continue

Validation du template CloudFormation :

aws cloudformation validate-template --template-body file://cloudformation/main.yml

### 🎯 CD – Déploiement Continu

Le CD, déclenché lors d'un push vers main, comporte deux phases :

1.  **Déploiement de l'Infrastructure** (via CloudFormation : EC2, SG, IAM, SNS).
    
2.  **Déploiement de l'Application** (Upload du JAR via SSH/SCP, Restart du service Spring Boot, Vérification de Santé).

4\. 📦 Structure Recommandée du Répertoire
------------------------------------------

/  ├── cloudformation/                 # Templates CloudFormation  

│   ├── ec2-web-db.yml                      

│   ├── network.yml               

│   ├── monitorCPUAndLogs.yml                

│   └── sns.yml                    

├── .github/ 

│   └── workflows/  

│       ├── deploy.yml 

├── db/ 

│   └── ini.sql #db script

├── src/ 

│   └── index.php  # Code source WebApp  

└── README.md                       # Ce fichier   `

5\. 📧 Notifications & Alertes
------------------------------

Grâce au service **SNS**, vous recevez des alertes en temps réel via email ou SMS en cas de :

*   **Performance** : La métrique CPU de la WebApp dépasse un seuil critique.
    
*   **Disponibilité** : L'application génère un nombre excessif d'erreurs (capturées par CloudWatch).
