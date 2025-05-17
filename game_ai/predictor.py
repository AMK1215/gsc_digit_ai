from sklearn.ensemble import RandomForestClassifier
import numpy as np

class AIPredictor:
    def __init__(self):
        self.model = RandomForestClassifier()
        self.history = []

    def add_result(self, digit):
        """Store game results for training."""
        self.history.append(digit)

    def train(self):
        """Train AI model on historical data."""
        if len(self.history) < 50:  # Need at least 50 spins
            return False
        
        X = np.array(self.history[:-1]).reshape(-1, 1)  # Previous digits
        y = np.array(self.history[1:])                  # Next digits
        self.model.fit(X, y)
        return True

    def predict_next(self):
        """Predict the next digit using AI."""
        if not self.train():  # Fallback if not enough data
            return np.random.randint(0, 10)
        last_digit = self.history[-1]
        return self.model.predict([[last_digit]])[0]