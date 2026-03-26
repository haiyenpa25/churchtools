import './bootstrap';
import { createApp } from 'vue';
import ApprovalCenter from './components/BibleLearning/ApprovalCenter.vue';
import FlashcardStudy from './components/BibleLearning/FlashcardStudy.vue';
import BiblePortal from './components/BibleLearning/BiblePortal.vue';
import EventTimeline from './components/BibleLearning/EventTimeline.vue';
import QuizSession from './components/BibleLearning/QuizSession.vue';
import KnowledgeGraph from './components/BibleLearning/KnowledgeGraph.vue';

const app = createApp({});

// Register Vue Components
app.component('approval-center', ApprovalCenter);
app.component('flashcard-study', FlashcardStudy);
app.component('bible-portal', BiblePortal);
app.component('event-timeline', EventTimeline);
app.component('quiz-session', QuizSession);
app.component('knowledge-graph', KnowledgeGraph);

// Mount to standard div id
app.mount('#app');
