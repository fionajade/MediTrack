<?php
/* ---------------- ERRORS FOR DEBUGGING ---------------- */
error_reporting(E_ALL);
ini_set('display_errors', 1);

/* ---------------- DATABASE CONFIG ---------------- */
$conn = new mysqli("127.0.0.1", "root", "", "pill-and-pestle");
if ($conn->connect_error) {
    echo json_encode(['ok' => false, 'reply' => 'Database connection failed']);
    exit;
}

/* ---------------- CONFIG ---------------- */
$NO_DATA_RESPONSE = "Sorry, I cannot answer that question based on the available medicine information.";
$OLLAMA_API_URL = 'http://127.0.0.1:11434/api/chat';
$MODEL = 'gemma3:270m';
$OLLAMA_TIMEOUT = 30;


/* ---------------- MEDICINE KEYWORDS ---------------- */
$medicineKeywords = [
    'ibuprofen',
    'acetaminophen',
    'paracetamol',
    'aspirin',
    'naproxen',
    'diclofenac',
    'tramadol',
    'morphine',
    'codeine',
    'ketorolac',
    'penicillin',
    'amoxicillin',
    'ciprofloxacin',
    'doxycycline',
    'azithromycin',
    'cephalexin',
    'clindamycin',
    'erythromycin',
    'metronidazole',
    'fluoxetine',
    'sertraline',
    'citalopram',
    'paroxetine',
    'venlafaxine',
    'duloxetine',
    'bupropion',
    'amitriptyline',
    'escitalopram',
    'insulin',
    'metformin',
    'glipizide',
    'glyburide',
    'pioglitazone',
    'sitagliptin',
    'empagliflozin',
    'liraglutide',
    'acarbose',
    'phenytoin',
    'lamotrigine',
    'valproic acid',
    'carbamazepine',
    'levetiracetam',
    'topiramate',
    'gabapentin',
    'clonazepam',
    'phenobarbital',
    'haloperidol',
    'risperidone',
    'olanzapine',
    'quetiapine',
    'aripiprazole',
    'clozapine',
    'chlorpromazine',
    'ziprasidone',
    'lurasidone',
    'acyclovir',
    'oseltamivir',
    'valacyclovir',
    'remdesivir',
    'zidovudine',
    'sofosbuvir',
    'lamivudine',
    'abacavir',
    'tenofovir',
    'warfarin',
    'heparin',
    'enoxaparin',
    'dabigatran',
    'apixaban',
    'rivaroxaban',
    'fondaparinux',
    'edoxaban',
    'diphenhydramine',
    'loratadine',
    'cetirizine',
    'fexofenadine',
    'chlorpheniramine',
    'hydroxyzine',
    'levocetirizine',
    'desloratadine',
    'promethazine'
];

/* ---------------- CATEGORY MAP ---------------- */
$categoryMap = [
    'pain' => 'Analgesics',
    'fever' => 'Analgesics',
    'infection' => 'Antibiotics',
    'bacterial' => 'Antibiotics',
    'depression' => 'Antidepressants',
    'diabetes' => 'Antidiabetics',
    'seizure' => 'Antiepileptics',
    'epilepsy' => 'Antiepileptics',
    'schizophrenia' => 'Antipsychotics',
    'bipolar' => 'Antipsychotics',
    'virus' => 'Antivirals',
    'viral' => 'Antivirals',
    'blood thinner' => 'Anticoagulants',
    'allergy' => 'Antihistamines'
];

/* ---------------- HANDLE CHAT ---------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');

    $userMsg = trim($_POST['message'] ?? '');
    if ($userMsg === '') {
        echo json_encode(['ok' => false, 'reply' => 'Empty message']);
        exit;
    }

    // Normalize input
    $msg = strtolower($userMsg);
    $msg = preg_replace('/[^a-z0-9\s]/', '', $msg);
    $msg = trim(preg_replace('/\s+/', ' ', $msg));

    $searchMode = null;
    $searchValue = null;

    /* ---------- 1. DIRECT MEDICINE MATCH ---------- */
    $msgWords = explode(' ', $msg);

    foreach ($medicineKeywords as $med) {
        $medLower = strtolower($med);

        foreach ($msgWords as $word) {
            if (
                $word === $medLower ||
                levenshtein($word, $medLower) <= 1
            ) {
                $searchMode = 'medicine';
                $searchValue = $med;
                break 2;
            }
        }
    }

    /* ---------- 2. DIRECT CATEGORY MATCH ---------- */
    if ($searchMode === null) {
        $validCategories = array_unique(array_values($categoryMap));

        foreach ($validCategories as $catName) {
            if (strpos($msg, strtolower($catName)) !== false) {
                $searchMode = 'category';
                $searchValue = $catName;
                break;
            }
        }
    }

    /* ---------- 3. KEYWORD TO CATEGORY ---------- */
    if ($searchMode === null) {
        foreach ($categoryMap as $key => $category) {
            if (strpos($msg, $key) !== false) {
                $searchMode = 'category';
                $searchValue = $category;
                break;
            }
        }
    }

    /* ---------- 4. DATABASE QUERY ---------- */
    $medicines = [];
    if ($searchMode !== null) {
        if ($searchMode === 'medicine') {
            $stmt = $conn->prepare("SELECT name, brand, description FROM medicines WHERE LOWER(name) LIKE ?");
            $like = '%' . strtolower($searchValue) . '%';
            $stmt->bind_param("s", $like);
        } else {
            $stmt = $conn->prepare(
                "SELECT m.name, m.brand, m.description
                 FROM medicines m
                 JOIN categories c ON m.category_id = c.id
                 WHERE c.name = ?"
            );
            $stmt->bind_param("s", $searchValue);
        }

        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            if (!empty($row['brand'])) {
                $row['brand'] = trim(preg_replace('/[\(\)]/', '', $row['brand']));
            }
            $medicines[] = $row;
        }
        $stmt->close();
    }

    /* ---------- 5. FORMAT OUTPUT FRIENDLY ---------- */
    if (!empty($medicines)) {
        $friendlyOutput = '';
        if ($searchMode === 'medicine') {
            $med = $medicines[0];
            $brandText = $med['brand'] ? " ({$med['brand']})" : '';
            $descText = $med['description'] ? " – {$med['description']}" : '';
            $friendlyOutput = "Here is the information for <strong>{$med['name']}</strong>{$brandText}: {$descText}";
        } else {
            $friendlyOutput = "Here are the medicines in the <strong>{$searchValue}</strong> category:<br>";
            foreach ($medicines as $i => $med) {
                $brandText = $med['brand'] ? " ({$med['brand']})" : '';
                $descText = $med['description'] ? " – {$med['description']}" : '';
                $friendlyOutput .= ($i + 1) . ". <strong>{$med['name']}</strong>{$brandText}{$descText}<br>";
            }
        }
        $dbOutput = $friendlyOutput;
    } else {
        echo json_encode([
            'ok' => true,
            'reply' => $NO_DATA_RESPONSE
        ]);
        exit;
    }

    /* ---------- 6. SYSTEM PROMPT FOR AI ---------- */
    if ($searchMode === 'category') {
        $systemPromptContent =
            "You are a medical list formatter.\n" .
            "Your task is ONLY to format medicine names for the category: {$searchValue}.\n" .
            "STRICT RULES:\n" .
            "- Use ONLY the medicine names provided\n" .
            "- Do NOT add, remove, or rename medicines\n" .
            "- Do NOT include brands or descriptions\n" .
            "- Do NOT add medical advice\n" .
            "- Output EXACTLY like this:\n" .
            "Here are the {$searchValue} medicines:\n" .
            "1. MedicineName\n" .
            "2. MedicineName\n" .
            "...etc...\n\n" .
            "MEDICINE NAMES:\n$dbOutput";
    } else {
        // medicine search
        $systemPromptContent =
            "You are a medicine information assistant.\n" .
            "ONLY use the medicine name provided below.\n" .
            "Do NOT add medical advice or extra information.\n" .
            "Output the information as a simple, clear message.\n\n" .
            "MEDICINE NAME:\n$dbOutput";
    }

    $systemPrompt = [
        'role' => 'system',
        'content' => $systemPromptContent
    ];

    /* ---------- 7. CALL OLLAMA AI WITH STREAM-FRIENDLY PARSING ---------- */
    $postData = json_encode([
        'model' => $MODEL,
        'messages' => [
            $systemPrompt,
            ['role' => 'user', 'content' => $userMsg]
        ]
    ]);

    $ch = curl_init($OLLAMA_API_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, $OLLAMA_TIMEOUT);

    $result = curl_exec($ch);
    if ($result !== false) {
        // Ollama streams multiple JSON lines, so we merge content
        $lines = explode("\n", $result);
        $aiReply = '';
        foreach ($lines as $line) {
            $jsonLine = json_decode($line, true);
            if ($jsonLine && isset($jsonLine['message']['content'])) {
                $aiReply .= $jsonLine['message']['content'];
            }
        }
    }
    curl_close($ch);

    echo json_encode([
        'ok' => true,
        'reply' => trim($aiReply)
    ]);
    exit;
}

$conn->close();
?>


<!-- ================= CHATBOT UI ================= -->

<style>
    :root {
        --chat-primary: #002147;
        --chat-bg: #ffffff;
        --chat-width: 380px;
    }

    .chatbot-toggler {
        position: fixed;
        bottom: 30px;
        right: 30px;
        height: 60px;
        width: 60px;
        border-radius: 50%;
        background: var(--chat-primary);
        color: #fff;
        border: none;
        cursor: pointer;
        z-index: 9998;
    }

    .chatbot-sidebar {
        position: fixed;
        top: 0;
        right: -450px;
        width: var(--chat-width);
        height: 100%;
        background: var(--chat-bg);
        box-shadow: -5px 0 20px rgba(0, 0, 0, .15);
        display: flex;
        flex-direction: column;
        transition: right .4s ease;
        z-index: 9999;
    }

    .show-chatbot .chatbot-sidebar {
        right: 0;
    }


    .chat-header {
        background: var(--chat-primary);
        color: #fff;
        padding: 15px;
        display: flex;
        justify-content: space-between;
    }

    .chat-box {
        flex: 1;
        padding: 15px;
        overflow-y: auto;
        background: #f9f9f9;
    }

    .chat-message {
        max-width: 80%;
    }

    .chat-message.user {
        margin-left: auto;
        text-align: right;
    }

    .message-content {
        padding: 10px 14px;
        border-radius: 14px;
        margin-bottom: 10px;
        font-size: .9rem;
    }

    .user .message-content {
        background: var(--chat-primary);
        color: #fff;
    }

    .bot .message-content {
        background: #e9ecef;
    }


    .chat-input {
        display: flex;
        gap: 10px;
        padding: 10px;
        border-top: 1px solid #ddd;
    }

    .chat-input textarea {
        flex: 1;
        resize: none;
        height: 42px;
        border-radius: 20px;
        padding: 10px 14px;
    }

    .send-btn {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        border: none;
        background: var(--chat-primary);
        color: #fff;
    }
</style>


<!-- Floating Trigger Button -->
<button class="chatbot-toggler" onclick="toggleChat()">
    <!-- Message Bubble Icon -->
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
        stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
    </svg>
</button>


<!-- The Sidebar -->
<div class="chatbot-sidebar">
    <div class="chat-header">
        <div style="display:flex; align-items:center; gap:10px;">
            <!-- Logo -->
            <div
                style="width:30px; height:30px; background:white; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                <span style="color:#002147; font-weight:bold;">M</span>
            </div>
            <h2>MediTrack AI</h2>
        </div>
        <button class="close-btn" onclick="toggleChat()">&times;</button>
    </div>


    <div class="chat-box">
        <!-- Default Welcome Message -->
        <div class="chat-message bot">
            <div class="message-content">
                Hello! I'm your MediTrack Assistant. How can I help you find medicines today?
            </div>
        </div>
    </div>


    <div class="chat-input">
        <textarea placeholder="Type a message..." required></textarea>
        <button class="send-btn">
            <!-- Send Icon -->
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <line x1="22" y1="2" x2="11" y2="13"></line>
                <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
            </svg>
        </button>
    </div>
</div>

<script>
    const body = document.body;
    const sendBtn = document.querySelector(".send-btn");
    const chatInput = document.querySelector(".chat-input textarea");
    const chatBox = document.querySelector(".chat-box");

    function toggleChat() {
        body.classList.toggle("show-chatbot");
    }

    function appendMessage(role, text) {
        if (!text) return;

        // Allow only safe tags
        const allowed = text.replace(/<(?!\/?(strong|br)\b)[^>]*>/gi, '');

        const formattedText = allowed
            .split('\n')
            .map(line => line.trim())
            .filter(line => line !== '')
            .join('<br>');

        chatBox.innerHTML += `
        <div class="chat-message ${role}">
            <div class="message-content">${formattedText}</div>
        </div>`;

        chatBox.scrollTop = chatBox.scrollHeight;
    }

    async function handleChat() {
        const msg = chatInput.value.trim();
        if (!msg) return;

        // Refrain user from typing while AI is processing output
        chatInput.disabled = true;
        sendBtn.disabled = true;

        appendMessage("user", msg);
        chatInput.value = "";

        appendMessage("bot", "Typing...");

        try {
            const res = await fetch("chatbot.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: new URLSearchParams({ action: "send", message: msg })
            });

            const data = await res.json();

            // Remove the "Typing..." message
            const lastMessage = chatBox.lastElementChild;
            if (lastMessage && lastMessage.querySelector('.message-content').textContent === "Typing...") {
                lastMessage.remove();
            }

    appendMessage("bot", data.ok ? data.reply : "Error occurred.");
        } catch (err) {
            appendMessage("bot", "Unable to connect.");
        } finally {
            // Re-enable input after AI finishes
            chatInput.disabled = false;
            sendBtn.disabled = false;
            chatInput.focus();
        }
    }
    sendBtn.onclick = handleChat;
    chatInput.onkeydown = e => {
        if (e.key === "Enter" && !e.shiftKey) {
            e.preventDefault();
            handleChat();
        }
    };

</script>