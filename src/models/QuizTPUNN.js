class QuizTPUNN {
    constructor(dbConnection) {
        this.conn = dbConnection;
        this.tableQuiz = 'quizzes';
        this.tableQuestion = 'questions';
        this.tableAnswer = 'answers';
    }

    /**
     * Récupère tous les quizzes
     */
    async getAllQuizzes() {
        const query = `SELECT * FROM ${this.tableQuiz}`;
        try {
            const [rows] = await this.conn.execute(query);
            return rows;
        } catch (error) {
            console.error("Erreur lors de la récupération des quizzes :", error.message);
            throw error;
        }
    }

    /**
     * Récupère les réponses pour une question donnée
     */
    async getAnswersByQuestionId(questionId) {
        const query = `SELECT * FROM ${this.tableAnswer} WHERE question_id = ?`;
        try {
            const [rows] = await this.conn.execute(query, [questionId]);
            return rows;
        } catch (error) {
            console.error("Erreur lors de la récupération des réponses :", error.message);
            throw error;
        }
    }

    /**
     * Récupère les questions pour un quiz donné, avec leurs réponses
     */
    async getQuestionsByQuizId(quizId) {
        const query = `SELECT * FROM ${this.tableQuestion} WHERE quiz_id = ?`;
        try {
            const [questions] = await this.conn.execute(query, [quizId]);

            for (const question of questions) {
                question.answers = await this.getAnswersByQuestionId(question.id);
            }

            return questions;
        } catch (error) {
            console.error("Erreur lors de la récupération des questions :", error.message);
            throw error;
        }
    }

    /**
     * Récupère un quiz par son ID, avec ses questions et réponses
     */
    async getQuizById(quizId) {
        const query = `SELECT * FROM ${this.tableQuiz} WHERE id = ?`;
        try {
            const [rows] = await this.conn.execute(query, [quizId]);
            if (rows.length === 0) {
                throw new Error("Quiz non trouvé");
            }

            const quiz = rows[0];
            quiz.questions = await this.getQuestionsByQuizId(quizId);
            return quiz;
        } catch (error) {
            console.error("Erreur lors de la récupération du quiz :", error.message);
            throw error;
        }
    }

    /**
     * Ajoute un nouveau quiz avec ses questions et réponses
     */
    async addQuiz(titre, questions) {
        try {
            await this.conn.beginTransaction();

            // Insérer le quiz
            const quizQuery = `INSERT INTO ${this.tableQuiz} (titre) VALUES (?)`;
            const [quizResult] = await this.conn.execute(quizQuery, [titre]);
            const quizId = quizResult.insertId;

            // Insérer les questions et réponses
            for (const question of questions) {
                const questionQuery = `
                    INSERT INTO ${this.tableQuestion} (quiz_id, question_text)
                    VALUES (?, ?)
                `;
                const [questionResult] = await this.conn.execute(questionQuery, [quizId, question.question_text]);
                const questionId = questionResult.insertId;

                for (const answer of question.answers) {
                    const isCorrect = answer.is_correct ? 1 : 0;
                    const answerQuery = `
                        INSERT INTO ${this.tableAnswer} (question_id, answer_text, is_correct)
                        VALUES (?, ?, ?)
                    `;
                    await this.conn.execute(answerQuery, [questionId, answer.answer_text, isCorrect]);
                }
            }

            await this.conn.commit();
            return quizId;
        } catch (error) {
            await this.conn.rollback();
            console.error("Erreur lors de l'ajout du quiz :", error.message);
            throw error;
        }
    }

    /**
     * Met à jour un quiz, ses questions et ses réponses
     */
    async updateQuiz(id, titre, questions) {
        try {
            const quizQuery = `UPDATE ${this.tableQuiz} SET titre = ? WHERE id = ?`;
            await this.conn.execute(quizQuery, [titre, id]);

            for (const question of questions) {
                if (question.id) {
                    const questionQuery = `
                        UPDATE ${this.tableQuestion}
                        SET question_text = ?
                        WHERE id = ?
                    `;
                    await this.conn.execute(questionQuery, [question.question_text, question.id]);
                } else {
                    const questionQuery = `
                        INSERT INTO ${this.tableQuestion} (quiz_id, question_text)
                        VALUES (?, ?)
                    `;
                    const [questionResult] = await this.conn.execute(questionQuery, [id, question.question_text]);
                    question.id = questionResult.insertId;
                }

                for (const answer of question.answers) {
                    if (answer.id) {
                        const answerQuery = `
                            UPDATE ${this.tableAnswer}
                            SET answer_text = ?, is_correct = ?
                            WHERE id = ?
                        `;
                        await this.conn.execute(answerQuery, [answer.answer_text, answer.is_correct ? 1 : 0, answer.id]);
                    } else {
                        const answerQuery = `
                            INSERT INTO ${this.tableAnswer} (question_id, answer_text, is_correct)
                            VALUES (?, ?, ?)
                        `;
                        await this.conn.execute(answerQuery, [question.id, answer.answer_text, answer.is_correct ? 1 : 0]);
                    }
                }
            }
        } catch (error) {
            console.error("Erreur lors de la mise à jour du quiz :", error.message);
            throw error;
        }
    }

    /**
     * Supprime un quiz
     */
    async deleteQuiz(id) {
        const query = `DELETE FROM ${this.tableQuiz} WHERE id = ?`;
        try {
            const [result] = await this.conn.execute(query, [id]);
            return result.affectedRows > 0;
        } catch (error) {
            console.error("Erreur lors de la suppression du quiz :", error.message);
            throw error;
        }
    }

    /**
     * Calcule le score d'un utilisateur pour un quiz
     */
    async calculateScore(quizId, userAnswers) {
        let score = 0;

        try {
            for (const [questionId, answerId] of Object.entries(userAnswers)) {
                const query = `
                    SELECT is_correct
                    FROM ${this.tableAnswer}
                    WHERE id = ? AND question_id = ?
                `;
                const [rows] = await this.conn.execute(query, [answerId, questionId]);

                if (rows.length > 0 && rows[0].is_correct) {
                    score++;
                }
            }

            return score;
        } catch (error) {
            console.error("Erreur lors du calcul du score :", error.message);
            throw error;
        }
    }
}

export default QuizTPUNN;
