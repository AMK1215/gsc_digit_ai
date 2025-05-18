from fastapi import FastAPI, Request
from pydantic import BaseModel
from typing import Literal
import numpy as np
from sklearn.ensemble import RandomForestClassifier

# ---------------- Core Classes ---------------- #
class DigitGame:
    def __init__(self):
        self.current_digit = None
        self.history = []

    def spin(self):
        self.current_digit = np.random.randint(0, 10)
        self.history.append(self.current_digit)
        return self.current_digit

    def get_color(self, digit):
        if 0 <= digit <= 2:
            return "Red"
        elif 3 <= digit <= 5:
            return "Blue"
        return "Green"

class AIPredictor:
    def __init__(self):
        self.model = RandomForestClassifier()
        self.history = []

    def add_result(self, digit):
        self.history.append(digit)

    def train(self):
        if len(self.history) < 20:
            return False
        X = np.array(self.history[:-1]).reshape(-1, 1)
        y = np.array(self.history[1:])
        self.model.fit(X, y)
        return True

    def predict_next(self):
        if not self.train() or not self.history:
            return np.random.randint(0, 10)
        return self.model.predict([[self.history[-1]]])[0]

class GameInterface:
    def __init__(self):
        self.game = DigitGame()
        self.ai = AIPredictor()
        self.balance = 1000

    def place_bet(self, bet_type, prediction, amount):
        result = self.game.spin()
        self.ai.add_result(result)
        color = self.game.get_color(result)

        if bet_type == "digit":
            win = (result == prediction)
            payout = amount * 9
        else:
            win = (color == prediction)
            payout = amount * 2

        if win:
            self.balance += payout
            return {"result": result, "color": color, "win": True, "balance": self.balance}
        else:
            self.balance -= amount
            return {"result": result, "color": color, "win": False, "balance": self.balance}

    def ai_suggestion(self):
        if len(self.ai.history) >= 5:
            next_digit = self.ai.predict_next()
            color = self.game.get_color(next_digit)
            return {"digit": next_digit, "color": color}
        return {"message": "AI needs more data (spin 5+ times)."}

# ---------------- FastAPI Integration ---------------- #

app = FastAPI()
game = GameInterface()

class BetRequest(BaseModel):
    bet_type: Literal["digit", "color"]
    prediction: int | str
    amount: int

@app.get("/")
def root():
    return {"message": "🎲 Welcome to the Digit Prediction Game API!"}

@app.post("/bet")
def place_bet(bet: BetRequest):
    result = game.place_bet(bet.bet_type, bet.prediction, bet.amount)
    return result

@app.get("/ai_suggestion")
def ai_tip():
    return game.ai_suggestion()

@app.get("/balance")
def check_balance():
    return {"balance": game.balance}
