const display = document.getElementById('display');
const buttons = document.querySelectorAll('.calc-button');
let currentInput = ''; // Stores the current number being typed
let operator = null; // Stores the selected operator
let previousInput = ''; // Stores the first operand
let resultDisplayed = false; // Flag to check if a result has just been displayed

// Function to update the display
function updateDisplay(value) {
    display.textContent = value;
}

// Handle button clicks
buttons.forEach(button => {
    button.addEventListener('click', () => {
        const buttonValue = button.dataset.value;

        // Clear input after an equals operation if a new number is typed
        if (resultDisplayed && !isNaN(buttonValue) && buttonValue !== '.') {
            currentInput = '';
            resultDisplayed = false;
        }

        if (resultDisplayed && (buttonValue === '+' || buttonValue === '-' || buttonValue === '*' || buttonValue === '/')) {
            // If an operator is pressed after a result, use the result as the first operand
            previousInput = currentInput;
            currentInput = ''; // Clear current input for new number
            operator = buttonValue;
            resultDisplayed = false; // Reset the flag
            return; // Exit to avoid appending operator to currentInput
        }

        switch (buttonValue) {
            case 'C':
                currentInput = '';
                operator = null;
                previousInput = '';
                updateDisplay('0');
                resultDisplayed = false;
                break;
            case '+/-':
                if (currentInput) {
                    currentInput = (parseFloat(currentInput) * -1).toString();
                    updateDisplay(currentInput);
                }
                break;
            case '%':
                if (currentInput) {
                    currentInput = (parseFloat(currentInput) / 100).toString();
                    updateDisplay(currentInput);
                }
                break;
            case '=':
                if (operator && previousInput && currentInput) {
                    let calculation;
                    const prev = parseFloat(previousInput);
                    const current = parseFloat(currentInput);
                    switch (operator) {
                        case '+':
                            calculation = prev + current;
                            break;
                        case '-':
                            calculation = prev - current;
                            break;
                        case '*':
                            calculation = prev * current;
                            break;
                        case '/':
                            if (current === 0) {
                                updateDisplay('Error');
                                currentInput = '';
                                previousInput = '';
                                operator = null;
                                return;
                            }
                            calculation = prev / current;
                            break;
                        default:
                            return;
                    }
                    // Round to prevent floating point inaccuracies for display
                    currentInput = (Math.round(calculation * 1000000000) / 1000000000).toString();
                    updateDisplay(currentInput);
                    previousInput = ''; // Clear previous input after calculation
                    operator = null; // Clear operator
                    resultDisplayed = true; // Set flag as result is displayed
                }
                break;
            case '+':
            case '-':
            case '*':
            case '/':
                if (currentInput) {
                    if (previousInput && operator) {
                        // If previous operation exists, calculate it first
                        const prev = parseFloat(previousInput);
                        const current = parseFloat(currentInput);
                        let intermediateCalculation;
                        switch (operator) {
                            case '+': intermediateCalculation = prev + current; break;
                            case '-': intermediateCalculation = prev - current; break;
                            case '*': intermediateCalculation = prev * current; break;
                            case '/':
                                if (current === 0) {
                                    updateDisplay('Error');
                                    currentInput = '';
                                    previousInput = '';
                                    operator = null;
                                    return;
                                }
                                intermediateCalculation = prev / current; break;
                        }
                        previousInput = (Math.round(intermediateCalculation * 1000000000) / 1000000000).toString();
                        updateDisplay(previousInput);
                    } else {
                        previousInput = currentInput;
                    }
                    operator = buttonValue;
                    currentInput = ''; // Clear current input for new number
                }
                break;
            case '.':
                if (!currentInput.includes('.')) {
                    if (currentInput === '') { // If display is empty, start with "0."
                        currentInput = '0.';
                    } else {
                        currentInput += '.';
                    }
                    updateDisplay(currentInput);
                }
                break;
            default: // Number buttons
                if (currentInput.length < 15) { // Limit input length to prevent overflow
                    currentInput += buttonValue;
                    updateDisplay(currentInput);
                }
                break;
        }
    });
});

// Initialize display to 0 when the page loads
document.addEventListener('DOMContentLoaded', () => {
    updateDisplay('0');
});
