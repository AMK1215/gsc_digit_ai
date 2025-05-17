import numpy as np

class DigitGame:
    def __init__(self):
        self.current_digit = None
        self.history = []  # Track past results

    def spin(self):
        """Generate a random digit (0-9)."""
        self.current_digit = np.random.randint(0, 10)
        self.history.append(self.current_digit)
        return self.current_digit

    def get_color(self, digit):
        """Map digit to color."""
        if 0 <= digit <= 2:
            return "Red"
        elif 3 <= digit <= 5:
            return "Blue"
        else:
            return "Green"

    def get_stats(self):
        """Return last 10 results."""
        return {
            "last_digit": self.current_digit,
            "color": self.get_color(self.current_digit),
            "history": self.history[-10:]  # Last 10 spins
        }