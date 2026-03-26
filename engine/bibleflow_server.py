# engine/bibleflow_server.py
"""
BibleFlow AI Server

Uses FastAPI & WebSockets to receive audio chunks from the frontend,
transcribe them in actual real-time using faster-whisper, and strictly match
with the target Bible verse to provide "Stop-on-Error" karaoke feedback.
"""

import asyncio
import json
import os
import tempfile
import io
import wave
from fastapi import FastAPI, WebSocket, WebSocketDisconnect
from faster_whisper import WhisperModel

app = FastAPI()

# ── 1. INITIALIZE WHISPER MODEL ─────────────────────────────────────────────
# For real-time Vietnamese, 'base' provides the best balance of speed and accuracy on CPU.
print("Loading Faster-Whisper model (base) for ultra-fast CPU recognition...")
model = WhisperModel("base", device="cpu", compute_type="int8")
print("Model loaded successfully!")

# ── 2. HELPER: TEXT NORMALIZATION & ALIGNMENT ───────────────────────────────
import re

def normalize_text(text: str) -> list:
    """Strip punctuation and lowercase."""
    text = text.lower()
    text = re.sub(r'[^\w\s]', '', text)
    return [w for w in text.split() if w]

def count_matched_words(target_words: list, spoken_words: list) -> int:
    """
    Very simple continuous forward-matching algorithm for the Karaoke "Stop-on-Error".
    It tries to find how many words from the beginning of `target_words` have been 
    successfully spoken in `spoken_words`.
    """
    match_count = 0
    spoken_idx = 0
    
    while match_count < len(target_words) and spoken_idx < len(spoken_words):
        t_word = target_words[match_count]
        # Look ahead in spoken words (allow up to 2 padding/filler words)
        found = False
        for offset in range(3):
            if spoken_idx + offset < len(spoken_words):
                if spoken_words[spoken_idx + offset] == t_word:
                    match_count += 1
                    spoken_idx += offset + 1
                    found = True
                    break
        
        if not found:
            break  # Stop matching if we couldn't find the next target word
            
    return match_count

# ── 3. WEBSOCKET ENDPOINT ───────────────────────────────────────────────────
@app.websocket("/ws/karaoke")
async def websocket_endpoint(websocket: WebSocket):
    await websocket.accept()
    print("Client connected to Karaoke WS!")
    
    target_text = ""
    target_words = []
    
    # Store audio chunks for continuous recognition
    # In a production app, use PyAudio or Memory buffer + VAD (Voice Activity Detection).
    # Here we simulate by saving WebM blobs sent by the browser.
    temp_dir = tempfile.mkdtemp()
    audio_buffer_path = os.path.join(temp_dir, "buffer.webm")
    
    is_transcribing = False
    
    try:
        while True:
            # Receive text (init) or bytes (audio data)
            message = await websocket.receive()
            
            if "text" in message:
                data = json.loads(message["text"])
                if data.get("type") == "init":
                    target_text = data.get("target_text", "")
                    target_words = normalize_text(target_text)
                    print(f"Target initialized: {target_text}")
                    # Clear the buffer for a fresh recording session to ensure new headers
                    open(audio_buffer_path, 'wb').close()
            
            elif "bytes" in message:
                audio_bytes = message["bytes"]
                
                # Append to file
                with open(audio_buffer_path, "ab") as f:
                    f.write(audio_bytes)
                
                # Ensure the file has some data
                if os.path.getsize(audio_buffer_path) < 1000:
                    continue
                    
                # Prevent event loop blocking & backlog: 
                # If whisper is currently busy, we skip this trigger.
                # The audio buffer keeps growing and will be processed on the next free tick.
                if is_transcribing:
                    continue
                    
                is_transcribing = True

                # Wrapper function for the CPU-bound task
                def run_transcription():
                    segs, _ = model.transcribe(audio_buffer_path, beam_size=1, language="vi", vad_filter=False)
                    res = ""
                    try:
                        for s in segs: res += s.text + " "
                    except Exception:
                        pass
                    return res
                
                # Run Whisper transcription in a background thread!
                try:
                    transcript = await asyncio.to_thread(run_transcription)
                        
                    if not transcript.strip():
                        continue
                        
                    spoken_words = normalize_text(transcript)
                    
                    # Karaoke Stop-On-Error Logic
                    match_count = count_matched_words(target_words, spoken_words)
                    
                    is_error = False
                    if len(spoken_words) > match_count + 1:
                        is_error = True

                    # Send status back to Frontend
                    await websocket.send_json({
                        "transcript": transcript.strip(),
                        "match_count": match_count,
                        "is_error": is_error,
                        "spoken_words": spoken_words
                    })
                        
                except Exception as e:
                    pass
                finally:
                    is_transcribing = False

    except WebSocketDisconnect:
        print("Client disconnected.")
    except Exception as e:
        print(f"Error: {e}")
    finally:
        # Cleanup
        if os.path.exists(audio_buffer_path):
            os.remove(audio_buffer_path)

if __name__ == "__main__":
    import uvicorn
    print("Starting BibleFlow Server on ws://localhost:8000/ws/karaoke")
    uvicorn.run(app, host="0.0.0.0", port=8000)
