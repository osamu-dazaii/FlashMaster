// Global variables
let currentCard = null;
let totalCorrect = 0;
let totalAttempts = 0;

// Fetch a new card
function fetchCard() {
  const themeId = new URLSearchParams(window.location.search).get('theme_id');
  const learningArea = document.getElementById('learning-area');
  
  // Show loading
  learningArea.innerHTML = `
    <div class="loader">
      <i class="fas fa-spinner fa-spin"></i>
      <span>Loading cards...</span>
    </div>
  `;
  
  fetch(`api/get_card.php?theme_id=${themeId}`)
    .then(response => response.json())
    .then(data => {
      if (data.completed) {
        showCompletionMessage();
        return;
      }
      
      currentCard = data;
      displayCard(data);
    })
    .catch(error => {
      learningArea.innerHTML = `
        <div class="error-message">
          <i class="fas fa-exclamation-circle"></i>
          <p>Something went wrong. Please try again.</p>
          <button onclick="fetchCard()" class="btn btn-primary">Try Again</button>
        </div>
      `;
    });
}

// Display the card
function displayCard(card) {
  const learningArea = document.getElementById('learning-area');
  
  // Get all answers and shuffle them
  const answers = [
    card.correct_answer,
    card.wrong_answer1,
    card.wrong_answer2
  ].sort(() => Math.random() - 0.5);
  
  // Create HTML for the card
  learningArea.innerHTML = `
    <div class="flashcard">
      <div class="card-image">
        <img src="${card.image_url}" alt="Flashcard Image">
      </div>
      <div class="card-question">
        <h3>What is this?</h3>
      </div>
      <div class="answer-options">
        ${answers.map(answer => `
          <button class="answer-btn" onclick="submitAnswer('${answer}')">${answer}</button>
        `).join('')}
      </div>
    </div>
  `;
}

// Submit answer
function submitAnswer(answer) {
  if (!currentCard) return;
  
  totalAttempts++;
  
  fetch('api/submit_answer.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ 
      card_id: currentCard.card_id, 
      answer: answer 
    })
  })
  .then(response => response.json())
  .then(data => {
    const learningArea = document.getElementById('learning-area');
    const isCorrect = data.is_correct === 1;
    
    if (isCorrect) {
      totalCorrect++;
      
      // Show success feedback
      learningArea.innerHTML = `
        <div class="feedback-card correct">
          <div class="feedback-icon">
            <i class="fas fa-check-circle"></i>
          </div>
          <h3>Correct!</h3>
          <p>Great job! "${data.correct_answer}" is the right answer.</p>
          <button onclick="fetchCard()" class="btn btn-primary">Next Card</button>
        </div>
      `;
      
      // Update progress bar
      updateProgressBar();
      
    } else {
      // Show incorrect feedback
      learningArea.innerHTML = `
        <div class="feedback-card incorrect">
          <div class="feedback-icon">
            <i class="fas fa-times-circle"></i>
          </div>
          <h3>Incorrect</h3>
          <p>The correct answer is "${data.correct_answer}".</p>
          <div class="card-image">
            <img src="${currentCard.image_url}" alt="Flashcard Image">
          </div>
          <button onclick="fetchCard()" class="btn btn-primary">Next Card</button>
        </div>
      `;
    }
  })
  .catch(error => {
    console.error('Error submitting answer:', error);
  });
}

// Update progress bar after correct answer
function updateProgressBar() {
  const progressBar = document.querySelector('.progress-container .progress');
  const progressStats = document.querySelector('.progress-stats span:first-child');
  
  if (!progressBar || !progressStats) return;
  
  // Get current progress
  const progressText = progressStats.textContent;
  const parts = progressText.split(' of ');
  
  if (parts.length === 2) {
    let [learned, total] = parts;
    learned = parseInt(learned) + 1;
    total = parseInt(total);
    
    // Calculate new percentage
    const percentage = Math.round((learned / total) * 100);
    
    // Update UI
    progressBar.style.width = `${percentage}%`;
    progressStats.textContent = `${learned} of ${total} cards mastered`;
    document.querySelector('.progress-stats span:last-child').textContent = `${percentage}% complete`;
  }
}

// Show completion message when all cards are mastered
function showCompletionMessage() {
  document.getElementById('learning-area').style.display = 'none';
  document.getElementById('completion-message').style.display = 'block';
  
  // Start confetti animation
  const confettiSettings = { target: 'confetti-animation', max: 180 };
  const confetti = new ConfettiGenerator(confettiSettings);
  confetti.render();
  
  // Stop confetti after 5 seconds
  setTimeout(() => {
    confetti.clear();
  }, 5000);
}