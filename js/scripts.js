function calculateScore() {
    const form = document.getElementById('quizForm');
    let score = 0;

    // Correct answers
    const answers = {
        q1: 'b',
        q2: 'c',
        q3: ['b', 'c']
    };

    // Evaluate answers
    if (form.q1.value === answers.q1) {
        score++;
    }
    if (form.q2.value === answers.q2) {
        score++;
    }

    const q3Answers = Array.from(form.q3).filter(input => input.checked).map(input => input.value);
    if (JSON.stringify(q3Answers) === JSON.stringify(answers.q3)) {
        score++;
    }

    // Display score
    document.getElementById('score').innerText = `Votre score est de ${score} sur 3.`;
}
