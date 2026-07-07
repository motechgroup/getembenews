@php
    $polls = json_decode(\App\Models\Setting::get('simulated_polls', '[]'), true);
    $activePoll = !empty($polls) ? $polls[0] : [
        'id' => 'default_poll',
        'question' => 'What should Getembe County prioritize in the next budget cycle?',
        'options' => ['Road Networks', 'Youth Tech Hubs', 'Agriculture', 'Healthcare']
    ];

    $quizzes = json_decode(\App\Models\Setting::get('simulated_quizzes', '[]'), true);
    $activeQuiz = !empty($quizzes) ? $quizzes[0] : [
        'id' => 'default_quiz',
        'title' => 'Getembe County History & Culture Trivia',
        'questions_count' => 3
    ];
@endphp

<!-- Polls Widget -->
<div class="bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-lg p-5 space-y-4"
     x-data="{ 
        voted: localStorage.getItem('poll_voted_{{ $activePoll['id'] }}') === 'true',
        selectedOption: null,
        votes: [42, 28, 18, 12],
        totalVotes: 100,
        submitVote() {
            if (this.selectedOption === null) return;
            this.votes[this.selectedOption]++;
            this.totalVotes++;
            this.voted = true;
            localStorage.setItem('poll_voted_{{ $activePoll['id'] }}', 'true');
        }
     }">
    <h3 class="text-xs font-black uppercase text-[#C8102E] tracking-wider flex items-center border-b border-gray-100 dark:border-gray-800 pb-2">
        <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z"/>
        </svg>
        <span>Reader Poll</span>
    </h3>
    
    <div class="space-y-3 text-xs">
        <p class="font-bold text-gray-900 dark:text-white leading-snug">
            {{ $activePoll['question'] }}
        </p>

        <!-- Voting Form -->
        <template x-if="!voted">
            <div class="space-y-2">
                @foreach(($activePoll['options'] ?? []) as $idx => $opt)
                    <label class="flex items-center p-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-md cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-850 transition">
                        <input type="radio" name="poll_option" :value="{{ $idx }}" x-model="selectedOption" class="text-[#C8102E] focus:ring-[#C8102E] border-gray-300">
                        <span class="ml-2 font-medium text-gray-700 dark:text-gray-300">{{ $opt }}</span>
                    </label>
                @endforeach
                
                <button type="button" 
                        @click="submitVote"
                        :disabled="selectedOption === null"
                        class="w-full bg-[#C8102E] hover:bg-red-700 disabled:opacity-50 text-white font-bold py-2 rounded text-xs transition uppercase tracking-wider">
                    Submit Vote
                </button>
            </div>
        </template>

        <!-- Voting Results -->
        <template x-if="voted">
            <div class="space-y-3 pt-1">
                @foreach(($activePoll['options'] ?? []) as $idx => $opt)
                    <div class="space-y-1">
                        <div class="flex justify-between font-bold text-gray-800 dark:text-gray-200">
                            <span>{{ $opt }}</span>
                            <span x-text="Math.round((votes[{{ $idx }}] / totalVotes) * 100) + '%'"></span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-gray-800 h-2.5 rounded-full overflow-hidden">
                            <div class="bg-[#FF7900] h-full rounded-full transition-all duration-1000"
                                 :style="'width: ' + ((votes[{{ $idx }}] / totalVotes) * 100) + '%'"></div>
                        </div>
                    </div>
                @endforeach
                <div class="text-[10px] text-gray-400 dark:text-gray-550 font-semibold text-center pt-2">
                    Total Votes: <span x-text="totalVotes"></span> &bull; Thank you for participating!
                </div>
            </div>
        </template>
    </div>
</div>

<!-- Quizzes Widget -->
<div class="bg-white dark:bg-gray-955 border border-gray-200 dark:border-gray-850 rounded-lg p-5 space-y-4"
     x-data="{
        started: false,
        completed: false,
        currentIndex: 0,
        score: 0,
        selectedAnswer: null,
        isAnswerCorrect: null,
        quizTitle: '{{ $activeQuiz['title'] }}',
        questions: [
            {
                q: 'Which facility was recently launched in Getembe County to benefit the youth?',
                options: ['A modern stadium', 'A state-of-the-art tech and innovation hub', 'An agricultural training college'],
                correct: 1,
                explanation: 'Getembe County recently commissioned a modern technology hub focusing on software engineering and coding bootcamps.'
            },
            {
                q: 'What is the main cultural art attraction Kisii County is globally known for?',
                options: ['Traditional Beadwork', 'Soapstone Carvings', 'Pottery & Ceramics'],
                correct: 1,
                explanation: 'Kisii County (specifically Tabaka region) is world-famous for its soapstone carvings.'
            },
            {
                q: 'What is the primary currency utilized in Getembe News settings?',
                options: ['KSH (Kenyan Shilling)', 'USD (US Dollar)', 'EUR (Euro)'],
                correct: 0,
                explanation: 'Getembe News is based in Kisii, Kenya, and defaults to KSH (Kenyan Shilling).'
            }
        ],
        startQuiz() {
            this.started = true;
            this.completed = false;
            this.currentIndex = 0;
            this.score = 0;
            this.resetQuestionState();
        },
        resetQuestionState() {
            this.selectedAnswer = null;
            this.isAnswerCorrect = null;
        },
        selectAnswer(idx) {
            if (this.isAnswerCorrect !== null) return;
            this.selectedAnswer = idx;
            this.isAnswerCorrect = (idx === this.questions[this.currentIndex].correct);
            if (this.isAnswerCorrect) {
                this.score++;
            }
        },
        nextQuestion() {
            if (this.currentIndex + 1 < this.questions.length) {
                this.currentIndex++;
                this.resetQuestionState();
            } else {
                this.completed = true;
            }
        }
     }">
    <h3 class="text-xs font-black uppercase text-[#FF7900] tracking-wider flex items-center border-b border-gray-100 dark:border-gray-800 pb-2">
        <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364.364l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
        </svg>
        <span>Interactive Quiz</span>
    </h3>

    <div class="text-xs space-y-3">
        <!-- Start Screen -->
        <template x-if="!started && !completed">
            <div class="space-y-3 text-center py-2">
                <p class="font-bold text-gray-900 dark:text-white leading-snug" x-text="quizTitle"></p>
                <p class="text-[10px] text-gray-500">Test your knowledge on regional updates and cultural heritage!</p>
                <button type="button" @click="startQuiz" class="w-full bg-[#FF7900] hover:bg-amber-600 text-white font-bold py-2 rounded transition uppercase tracking-wider">
                    Start Quiz
                </button>
            </div>
        </template>

        <!-- Question Screen -->
        <template x-if="started && !completed">
            <div class="space-y-3">
                <div class="flex justify-between items-center text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                    <span x-text="'Question ' + (currentIndex + 1) + ' of ' + questions.length"></span>
                    <span x-text="'Score: ' + score"></span>
                </div>
                
                <p class="font-bold text-gray-950 dark:text-white leading-snug" x-text="questions[currentIndex].q"></p>
                
                <div class="space-y-2">
                    <template x-for="(opt, idx) in questions[currentIndex].options" :key="idx">
                        <button type="button" 
                                @click="selectAnswer(idx)"
                                :class="{
                                    'border-[#C8102E] bg-red-50 dark:bg-red-950/20 text-[#C8102E]': selectedAnswer === idx && !isAnswerCorrect,
                                    'border-green-600 bg-green-50 dark:bg-green-950/20 text-green-700 dark:text-green-455': selectedAnswer === idx && isAnswerCorrect,
                                    'border-green-600 text-green-700 dark:text-green-455': selectedAnswer !== idx && isAnswerCorrect !== null && idx === questions[currentIndex].correct,
                                    'border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900 hover:bg-gray-100 dark:hover:bg-gray-850': selectedAnswer !== idx && (isAnswerCorrect === null || idx !== questions[currentIndex].correct)
                                }"
                                class="w-full text-left p-2.5 rounded-md border font-medium transition flex items-center justify-between">
                            <span x-text="opt"></span>
                            <span class="text-[10px] font-bold uppercase shrink-0 ml-2">
                                <template x-if="selectedAnswer === idx && isAnswerCorrect">
                                    <span>✓ Correct</span>
                                </template>
                                <template x-if="selectedAnswer === idx && !isAnswerCorrect">
                                    <span>✗ Incorrect</span>
                                </template>
                            </span>
                        </button>
                    </template>
                </div>

                <!-- Explanation / Next -->
                <template x-if="isAnswerCorrect !== null">
                    <div class="space-y-3 pt-2 border-t border-gray-100 dark:border-gray-850">
                        <p class="text-[11px] text-gray-500 dark:text-gray-400" x-text="questions[currentIndex].explanation"></p>
                        <button type="button" @click="nextQuestion" class="w-full bg-gray-900 hover:bg-gray-800 dark:bg-white dark:hover:bg-gray-100 dark:text-black text-white font-bold py-2 rounded transition uppercase tracking-wider">
                            Next Question
                        </button>
                    </div>
                </template>
            </div>
        </template>

        <!-- Completed Screen -->
        <template x-if="completed">
            <div class="space-y-3 text-center py-4">
                <h4 class="font-black text-lg text-gray-900 dark:text-white">Quiz Finished!</h4>
                <div class="text-3xl font-black text-[#FF7900] tracking-tight">
                    <span x-text="score"></span> / <span x-text="questions.length"></span>
                </div>
                <p class="text-xs text-gray-500">
                    <span x-if="score === questions.length">Outstanding! You know Getembe News perfectly!</span>
                    <span x-if="score < questions.length">Good effort! Try again to get a perfect score.</span>
                </p>
                <div class="pt-2 flex gap-2">
                    <button type="button" @click="startQuiz" class="flex-1 bg-gray-100 dark:bg-gray-900 hover:bg-gray-200 text-gray-800 dark:text-gray-200 font-bold py-2 rounded transition text-[10px] uppercase tracking-wider">
                        Play Again
                    </button>
                    <button type="button" @click="started = false; completed = false;" class="flex-1 bg-[#FF7900] hover:bg-amber-600 text-white font-bold py-2 rounded transition text-[10px] uppercase tracking-wider">
                        Close
                    </button>
                </div>
            </div>
        </template>
    </div>
</div>
