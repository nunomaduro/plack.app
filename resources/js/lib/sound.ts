/**
 * A short synthesized chime for incoming messages the user isn't looking at —
 * evoking the classic mIRC message beep without shipping an audio asset.
 *
 * The tone is a soft two-step sine blip (880Hz → 660Hz) shaped with a quick
 * attack/decay envelope so it reads as a gentle "blip", not a harsh beep. The
 * AudioContext is created lazily and reused: browsers only allow audio after a
 * user gesture, and by the time a socket event fires the user has already been
 * interacting with the app.
 */

type WebkitWindow = Window & {
    webkitAudioContext?: typeof AudioContext;
};

let context: AudioContext | null = null;

function audioContext(): AudioContext | null {
    if (typeof window === 'undefined') {
        return null;
    }

    if (context) {
        return context;
    }

    const Ctor =
        window.AudioContext ?? (window as WebkitWindow).webkitAudioContext;

    if (!Ctor) {
        return null;
    }

    context = new Ctor();

    return context;
}

export function playMessageChime(): void {
    try {
        const ctx = audioContext();

        if (!ctx) {
            return;
        }

        // A suspended context (e.g. before the first gesture) can be resumed;
        // ignore the promise so a rejection never bubbles into the caller.
        if (ctx.state === 'suspended') {
            void ctx.resume();
        }

        const now = ctx.currentTime;
        const gain = ctx.createGain();
        const oscillator = ctx.createOscillator();

        oscillator.type = 'sine';
        oscillator.frequency.setValueAtTime(880, now);
        oscillator.frequency.setValueAtTime(660, now + 0.06);

        // Quick attack, then decay to near-silence over ~120ms.
        gain.gain.setValueAtTime(0.0001, now);
        gain.gain.exponentialRampToValueAtTime(0.15, now + 0.01);
        gain.gain.exponentialRampToValueAtTime(0.0001, now + 0.12);

        oscillator.connect(gain);
        gain.connect(ctx.destination);

        oscillator.start(now);
        oscillator.stop(now + 0.13);
    } catch {
        // Audio is best-effort; never let a blocked context break the handler.
    }
}
