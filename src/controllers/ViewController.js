// Import des connexions à la base de données
import DatabaseConnection from '../Database/DatabaseTPUNN.js';
import MongoDBConnection from '../Database/MongoDBConnectionTPUNN.js';
import MongoDBConnection from '../Database/MongoTPUNNForum.js';

// Import des modèles et repositories
import AvisMedicaux from '../models/AvisMedicaux.js';
import Comment from '../models/CommentTPUNN.js';
import FoodPresentation from '../models/FoodPresentation.js';
import Forum from '../models/ForumTPUNN.js';
import Guide from '../models/Guide.js';
import Profile from '../models/Profile.js';
import Quiz from '../models/QuizTPUNN.js';
import Recipe from '../models/Recipe.js';
import Response from '../models/ResponseTPUNN.js';
import Tracking from '../models/Tracking.js';
import User from '../models/UserOne.js';
import UserTwo from '../models/UserTwo.js';

// Import des contrôleurs
import AvisMedicauxController from '../controllers/AvisMedicauxController.js';
import CommentController from '../controllers/CommentController.js';
import FoodPresentationController from '../controllers/FoodPresentationController.js';
import ForumController from '../controllers/ForumController.js';
import GuideController from '../controllers/GuideController.js';
import ProfileController from '../controllers/ProfileController.js';
import QuizController from '../controllers/QuizController.js';
import RecipeController from '../controllers/RecipeController.js';
import ResponseController from '../controllers/ResponseController.js';
import TrackingController from '../controllers/TrackingController.js';
import UserController from '../controllers/UserController.js';
import UserTwoController from '../controllers/UserTwoController.js';

class ViewController {
    constructor() {
        this.controllers = {};
    }

    async initialize() {
        // Connexions aux bases de données
        const sqlConnection = await new DatabaseConnection().connect();
        const mongoConnection = await new MongoDBConnection().getCollection('clicks');

        // Instanciation des modèles
        const avisMedicauxModel = new AvisMedicaux(sqlConnection);
        const commentModel = new Comment(sqlConnection);
        const foodPresentationModel = new FoodPresentation(sqlConnection);
        const forumModel = new Forum(sqlConnection);
        const guideModel = new Guide(sqlConnection);
        const profileModel = new Profile(sqlConnection);
        const quizModel = new Quiz(sqlConnection);
        const recipeModel = new Recipe(sqlConnection);
        const responseModel = new Response(sqlConnection);
        const trackingModel = new Tracking(sqlConnection);
        const userModel = new User(sqlConnection);
        const userTwoModel = new UserTwo(sqlConnection);

        // Instanciation des contrôleurs avec leurs modèles respectifs
        this.controllers.avisMedicauxController = new AvisMedicauxController(avisMedicauxModel);
        this.controllers.commentController = new CommentController(commentModel);
        this.controllers.foodPresentationController = new FoodPresentationController(foodPresentationModel);
        this.controllers.forumController = new ForumController(forumModel);
        this.controllers.guideController = new GuideController(guideModel);
        this.controllers.profileController = new ProfileController(profileModel);
        this.controllers.quizController = new QuizController(quizModel);
        this.controllers.recipeController = new RecipeController(recipeModel);
        this.controllers.responseController = new ResponseController(responseModel);
        this.controllers.trackingController = new TrackingController(trackingModel);
        this.controllers.userController = new UserController(userModel);
        this.controllers.userTwoController = new UserTwoController(userTwoModel);

        console.log('Tous les contrôleurs ont été initialisés.');
    }

    getController(name) {
        return this.controllers[name] || null;
    }
}

export default new ViewController();
