import { useEffect, useId, useRef } from 'react';

export default function Modal({ title, onClose, children }) {
  const titleId = useId();
  const modalRef = useRef(null);

  useEffect(() => {
    const previousOverflow = document.body.style.overflow;
    const previouslyFocused = document.activeElement;
    const handleKeyDown = (event) => {
      if (event.key === 'Escape') onClose();
      if (event.key === 'Tab') {
        const focusable = [...modalRef.current.querySelectorAll('button, input, select, textarea, [href], [tabindex]:not([tabindex="-1"])')]
          .filter((element) => !element.disabled);
        const first = focusable[0];
        const last = focusable.at(-1);
        if (event.shiftKey && document.activeElement === first) {
          event.preventDefault();
          last?.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
          event.preventDefault();
          first?.focus();
        }
      }
    };

    document.body.style.overflow = 'hidden';
    document.addEventListener('keydown', handleKeyDown);
    return () => {
      document.body.style.overflow = previousOverflow;
      document.removeEventListener('keydown', handleKeyDown);
      previouslyFocused?.focus();
    };
  }, [onClose]);

  return <div className="modal-backdrop" onMouseDown={(event) => { if (event.target === event.currentTarget) onClose(); }}>
    <section ref={modalRef} className="modal" role="dialog" aria-modal="true" aria-labelledby={titleId}>
      <header className="modal-header">
        <h2 id={titleId}>{title}</h2>
        <button type="button" className="modal-close" onClick={onClose} aria-label="Close modal" autoFocus>&times;</button>
      </header>
      <div className="modal-body">{children}</div>
    </section>
  </div>;
}
