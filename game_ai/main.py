import numpy as np
from sklearn.ensemble import RandomForestClassifier

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
        color = self.game.get_color(result)  # Moved color definition here

        if bet_type == "digit":
            win = (result == prediction)
            payout = amount * 9
        else:  # Color bet
            win = (color == prediction)
            payout = amount * 2

        if win:
            self.balance += payout
            return f"🎉 WIN! Result: {result} ({color}). Balance: {self.balance}"
        self.balance -= amount
        return f"❌ LOST. Result: {result} ({color}). Balance: {self.balance}"

    def ai_suggestion(self):
        if len(self.ai.history) >= 5:  # Reduced threshold to 5 spins
            next_digit = self.ai.predict_next()
            color = self.game.get_color(next_digit)
            return f"🤖 AI suggests: Bet on {next_digit} ({color})"
        return "AI needs more data (spin 5+ times)."

def main():
    game = GameInterface()
    print("🎲 DIGIT PREDICTION GAME (0-9)")
    print("💰 Starting balance: 1000")

    while game.balance > 0:
        print("\n1. Bet on Digit | 2. Bet on Color | 3. AI Suggestion | 4. Quit")
        choice = input("Choose: ").strip()

        if choice == "4":
            break
        elif choice == "3":
            print(game.ai_suggestion())
            continue

        try:
            amount = int(input("💰 Bet amount: "))
            if amount > game.balance:
                print("❌ Not enough balance!")
                continue

            if choice == "1":
                digit = int(input("🔢 Digit (0-9): "))
                print(game.place_bet("digit", digit, amount))
            elif choice == "2":
                color = input("🎨 Color (Red/Blue/Green): ").capitalize()
                print(game.place_bet("color", color, amount))
            else:
                print("❌ Invalid choice!")
        except ValueError:
            print("❌ Invalid input!")

    print(f"🏁 Game over! Final balance: {game.balance}")

if __name__ == "__main__":
    main()