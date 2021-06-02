#!/usr/bin/env python
# -*- coding: utf-8 -*-

# Import to support hex to IEEE 32bit decimal converter
import struct, binascii
# Import to support Modbus communicator
from pyModbusTCP.client import ModbusClient
# Import to access commandline args
import argparse

# Import time to sleep
import time

#define the 4bit registers
reg_array_4bit = [
                    ['Active Energy',3216, 'kWh'], 
                    ['Reactive Energy',3232, 'kVARh'],
                    ['Apparent Energy',3244, 'kVAh'] 
                 ]
#define the 2bit registers
reg_array_2bit = [
                    ['Current I a',3000, 'A'], 
                    ['Current I b',3002, 'A'], 
                    ['Current I c',3004, 'A'], 
                    ['Current I 4',3006, 'A'], 
                    ['Current I 5',3008, 'A'], 
                    ['Current I Average',3010, 'A'], 
                    ['Current Unbalance',3018, "%"], 
                    ['Voltage L-L ab',3020, 'V'], 
                    ['Voltage L-L bc',3022, 'V'], 
                    ['Voltage L-L ca',3024, 'V'], 
                    ['Voltage L-L Average',3026, 'V'], 
                    ['Voltage L-N a',3028, 'V'], 
                    ['Voltage L-N b',3030, 'V'], 
                    ['Voltage L-N c',3032, 'V'], 
                    ['Voltage L-N Average',3036, 'V'], 
                    ['Voltage Unbalanced L-N',3052, '%'], 
                    ['Active Power',3060, 'W'], 
                    ['Reactive Power',3068, 'VAR'], 
                    ['Apparent Power',3076, 'VA'], 
                    ['Voltage L-L Average',21024, 'V'], 
                    ['Voltage L-N Average',21034, 'V'], 
                    ['Frequency',27616, 'Hz'] 
                 ]

#initialize the Modbus TCP client
c = ModbusClient()

# Enable debugging in case of errors
#c.debug(True)

#time.sleep(1)

# Parse console arguments
parser = argparse.ArgumentParser("Read meter over ModBusTCP")
parser.add_argument("host", help="The ModBusTCP host address.", type=str)
parser.add_argument("port", help="The ModBusTCP port number.", type=int)
args = parser.parse_args()
#print(args.host," ",args.port)

if not c.host(args.host):
    print("host error")
# Define port
if not c.port(args.port):
    print("port error")


#time.sleep(1)

# Open the connection
if c.open():

    print("[")
    
    count_tot = 0
    count_len = len(reg_array_4bit) + len(reg_array_2bit) - 1
    
    # Poll the 4bit registers
    for reg_num in reg_array_4bit:
        # Reduce poll_reg by 1 cause the class doesn't work
        poll_reg = reg_num[1] - 1 
        
        # Get individual values from the registers
        regs_list = c.read_holding_registers(poll_reg, 4)
        #print(regs_list)
        
        # Convert it into integer by multiplying with 2^n for each bit
        tot_int = regs_list[0]*281474976710656 + regs_list[1]*4294967296 + regs_list[2]*65536 + regs_list[3]*1
        
        # Convert it to K by dividing by 1000
        tot_int_k = float(tot_int) / 1000
        
        # Print the valuess
        print( '[ "{}", "{}", "{}", "{}" ],'.format( reg_num[0], reg_num[1], tot_int_k, reg_num[2]) )
        
        count_tot+=1
        
    
    # Poll the 2bit registers
    for reg_num in reg_array_2bit:
        # Reduce poll_reg by 1 cause the class doesn't work
        poll_reg = reg_num[1] - 1 
        
        # Get individual values from the registers
        regs_list = c.read_holding_registers(poll_reg, 2)
        #print(regs_list)
        
        # Convert the bits into hex and combine the registers into 1 value
        tot_hex = "00000000{:04x}{:04x}".format(regs_list[0],regs_list[1])
        # Convert the new hex value into a IEEE 32bit decimal value (import struct, binascii)
        tot_dec = struct.unpack('>ff', binascii.unhexlify(tot_hex))
        
        if (count_tot==count_len):
            end_str = "]"
        else:
            end_str = "],"
        
        # Print the valuess
        print( '[ "{}", "{}", "{}", "{}" {}'.format( reg_num[0], reg_num[1], tot_dec[1], reg_num[2], end_str) )
        
        count_tot+=1
        
    print("]")
    # Close the connection
    c.close()


#time.sleep(1)
